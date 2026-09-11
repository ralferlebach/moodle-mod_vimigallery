<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace mod_vimigallery\source;

/**
 * Maps stored in a ViMi Pad field of a Database (mod_data) activity.
 *
 * The values of a `datafield_vimipad` field across the database's entries become
 * the gallery's maps. Visibility strictly follows the source database: the viewer
 * must be able to view its entries, unapproved entries are hidden unless the
 * viewer may approve (or owns them), and separate groups are honoured.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class datafield_source implements source_interface {
    /** @var int The course module id of the source database. */
    protected int $cmid;

    /** @var int The id of the ViMi Pad data field. */
    protected int $fieldid;

    /**
     * Constructor.
     *
     * @param int $cmid The course module id of the source database.
     * @param int $fieldid The id of the ViMi Pad data field.
     */
    public function __construct(int $cmid, int $fieldid) {
        $this->cmid = $cmid;
        $this->fieldid = $fieldid;
    }

    /**
     * The visible maps of the source field for the viewer.
     *
     * @param int|null $userid The viewer, or null for the current user.
     * @return \stdClass[] The visible maps, in entry order.
     */
    public function get_items(?int $userid = null): array {
        global $USER;

        $userid = $userid ?? (int) $USER->id;

        $source = $this->resolve_source($userid);
        if ($source === null) {
            return [];
        }

        return $this->build_items($this->fetch_records($source, $userid));
    }

    /**
     * Resolve the source database module and field, applying every access check.
     *
     * Returns null whenever the source cannot or must not be read: the peer
     * plugin is absent, the module is gone or invisible, the viewer lacks
     * mod/data:viewentry, or the configured field does not belong to this
     * database or is not a ViMi Pad field.
     *
     * @param int $userid The viewer.
     * @return object|null Object with cm, context and data, or null.
     */
    protected function resolve_source(int $userid) {
        global $DB;

        // The datafield_vimipad plugin is an optional peer: without it there are
        // no ViMi Pad fields to read, so degrade to empty instead of failing.
        if (!\core_component::get_plugin_directory('datafield', 'vimipad')) {
            return null;
        }

        $cm = get_coursemodule_from_id('data', $this->cmid, 0, false, IGNORE_MISSING);
        if (!$cm) {
            return null;
        }
        $modinfo = get_fast_modinfo($cm->course, $userid);
        if (!isset($modinfo->cms[$cm->id]) || !$modinfo->cms[$cm->id]->uservisible) {
            return null;
        }
        $context = \context_module::instance($cm->id);
        if (!has_capability('mod/data:viewentry', $context, $userid)) {
            return null;
        }
        // The field must belong to this database and be a ViMi Pad field.
        $field = $DB->get_record('data_fields', ['id' => $this->fieldid, 'dataid' => $cm->instance]);
        if (!$field || $field->type !== 'vimipad') {
            return null;
        }

        return (object) [
            'cm' => $cm,
            'context' => $context,
            'data' => $DB->get_record('data', ['id' => $cm->instance], '*', MUST_EXIST),
        ];
    }

    /**
     * Fetch the visible entries for this viewer, newest first and bounded, then
     * restored to chronological order.
     *
     * @param object $source The resolved source (cm, context, data).
     * @param int $userid The viewer.
     * @return array The entry records.
     */
    protected function fetch_records($source, int $userid): array {
        global $DB;

        $params = ['dataid' => $source->cm->instance, 'fieldid' => $this->fieldid];
        $where = ['r.dataid = :dataid'];

        foreach ($this->visibility_clauses($source, $userid) as $clause) {
            $where[] = $clause->sql;
            $params += $clause->params;
        }

        $sql = "SELECT r.id, r.userid, c.content AS mapjson
                  FROM {data_records} r
                  JOIN {data_content} c ON c.recordid = r.id AND c.fieldid = :fieldid
                 WHERE " . implode(' AND ', $where) . "
              ORDER BY r.timecreated DESC, r.id DESC";

        // Bounded: a database with thousands of entries must not become one page.
        return array_reverse($DB->get_records_sql($sql, $params, 0, self::MAX_ITEMS), true);
    }

    /**
     * The approval and group restrictions that apply to this viewer.
     *
     * @param object $source The resolved source (cm, context, data).
     * @param int $userid The viewer.
     * @return array List of objects with sql and params.
     */
    protected function visibility_clauses($source, int $userid): array {
        global $DB;

        $clauses = [];

        // Approval: hide unapproved entries unless the viewer may approve them
        // or owns them.
        if (!empty($source->data->approval) && !has_capability('mod/data:approve', $source->context, $userid)) {
            $clauses[] = (object) [
                'sql' => '(r.approved = 1 OR r.userid = :owner)',
                'params' => ['owner' => $userid],
            ];
        }

        // Separate groups: restrict to the viewer's groups (plus group-0 entries)
        // unless the viewer may access all groups.
        $groupmode = groups_get_activity_groupmode($source->cm);
        if ($groupmode != SEPARATEGROUPS || has_capability('moodle/site:accessallgroups', $source->context, $userid)) {
            return $clauses;
        }

        $usergroups = groups_get_user_groups($source->cm->course, $userid);
        $groupids = $usergroups[0] ?? [];
        if (empty($groupids)) {
            $clauses[] = (object) ['sql' => 'r.groupid = 0', 'params' => []];
            return $clauses;
        }

        [$ingroup, $groupparams] = $DB->get_in_or_equal($groupids, SQL_PARAMS_NAMED, 'grp');
        $clauses[] = (object) [
            'sql' => "(r.groupid = 0 OR r.groupid $ingroup)",
            'params' => $groupparams,
        ];
        return $clauses;
    }

    /**
     * Turn entry records into gallery items, skipping anything that is not a map.
     *
     * @param array $records The entry records.
     * @return array The gallery items.
     */
    protected function build_items(array $records): array {
        // Author names in one query rather than one per record.
        $usercache = \mod_vimigallery\local\comment_service::author_names(
            array_map(fn($r) => (int) $r->userid, $records)
        );

        $items = [];
        $sortorder = 0;
        foreach ($records as $record) {
            $decoded = $this->decode_map($record->mapjson);
            if ($decoded === null) {
                continue;
            }

            $item = new \stdClass();
            $item->id = 'df' . $record->id;
            $item->mapjson = $record->mapjson;
            $item->profile = \core_text::substr($decoded, 0, 40);
            $item->authorname = \core_text::substr($usercache[(int) $record->userid] ?? '', 0, 255);
            $item->sourceuserid = (int) $record->userid;
            $item->sortorder = $sortorder++;
            $item->visible = 1;
            $items[] = $item;
        }

        return $items;
    }

    /**
     * The profile of a stored map, or null when the value is not a usable map.
     *
     * @param string|null $mapjson The stored field value.
     * @return string|null The profile key, or null to skip this entry.
     */
    protected function decode_map($mapjson): ?string {
        if ($mapjson === null || trim((string) $mapjson) === '') {
            return null;
        }
        $decoded = json_decode($mapjson, true);
        if (!is_array($decoded) || !isset($decoded['nodes'])) {
            return null;
        }
        return isset($decoded['profile']) && is_string($decoded['profile'])
            ? $decoded['profile'] : 'conceptmap';
    }
}
