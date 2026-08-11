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
        global $DB, $USER;

        $userid = $userid ?? (int) $USER->id;

        // Resolve the source database module; bail out quietly if it is gone.
        $cm = get_coursemodule_from_id('data', $this->cmid, 0, false, IGNORE_MISSING);
        if (!$cm) {
            return [];
        }
        $modinfo = get_fast_modinfo($cm->course, $userid);
        if (!isset($modinfo->cms[$cm->id]) || !$modinfo->cms[$cm->id]->uservisible) {
            return [];
        }
        $context = \context_module::instance($cm->id);
        if (!has_capability('mod/data:viewentry', $context, $userid)) {
            return [];
        }
        // The field must belong to this database and be a ViMi Pad field.
        $field = $DB->get_record('data_fields', ['id' => $this->fieldid, 'dataid' => $cm->instance]);
        if (!$field || $field->type !== 'vimipad') {
            return [];
        }
        $data = $DB->get_record('data', ['id' => $cm->instance], '*', MUST_EXIST);

        $params = ['dataid' => $cm->instance, 'fieldid' => $this->fieldid];
        $where = ['r.dataid = :dataid'];

        // Approval: hide unapproved entries unless the viewer may approve them or
        // owns them.
        if (!empty($data->approval) && !has_capability('mod/data:approve', $context, $userid)) {
            $where[] = '(r.approved = 1 OR r.userid = :owner)';
            $params['owner'] = $userid;
        }

        // Separate groups: restrict to the viewer's groups (plus group-0 entries)
        // unless the viewer may access all groups.
        $groupmode = groups_get_activity_groupmode($cm);
        if (
            $groupmode == SEPARATEGROUPS
                && !has_capability('moodle/site:accessallgroups', $context, $userid)
        ) {
            $usergroups = groups_get_user_groups($cm->course, $userid);
            $groupids = $usergroups[0] ?? [];
            if (empty($groupids)) {
                $where[] = 'r.groupid = 0';
            } else {
                [$ingroup, $groupparams] = $DB->get_in_or_equal(
                    $groupids,
                    SQL_PARAMS_NAMED,
                    'grp'
                );
                $where[] = "(r.groupid = 0 OR r.groupid $ingroup)";
                $params += $groupparams;
            }
        }

        $sql = "SELECT r.id, r.userid, c.content AS mapjson
                  FROM {data_records} r
                  JOIN {data_content} c ON c.recordid = r.id AND c.fieldid = :fieldid
                 WHERE " . implode(' AND ', $where) . "
              ORDER BY r.timecreated ASC, r.id ASC";
        $records = $DB->get_records_sql($sql, $params);

        $items = [];
        $sortorder = 0;
        $usercache = [];
        foreach ($records as $record) {
            if ($record->mapjson === null || trim($record->mapjson) === '') {
                continue;
            }
            $decoded = json_decode($record->mapjson, true);
            if (!is_array($decoded) || !isset($decoded['nodes'])) {
                continue;
            }
            $profile = isset($decoded['profile']) && is_string($decoded['profile'])
                ? $decoded['profile'] : 'conceptmap';

            $author = '';
            if (!isset($usercache[$record->userid])) {
                $user = \core_user::get_user($record->userid, '*', IGNORE_MISSING);
                $usercache[$record->userid] = $user ? fullname($user) : '';
            }
            $author = $usercache[$record->userid];

            $item = new \stdClass();
            $item->id = 'df' . $record->id;
            $item->mapjson = $record->mapjson;
            $item->profile = \core_text::substr($profile, 0, 40);
            $item->authorname = \core_text::substr($author, 0, 255);
            $item->sortorder = $sortorder++;
            $item->visible = 1;
            $items[] = $item;
        }

        return $items;
    }
}
