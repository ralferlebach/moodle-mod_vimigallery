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
 * Maps from a ViMi Pad (mod_vimipad) activity.
 *
 * In reference mode the gallery shows the activity's model solution; because that
 * reveals the answer, it is shown live only to graders (a teacher may publish it
 * to everyone with a static or snapshot freshness). In submissions mode it shows
 * the submitted maps: a learner sees only their own (and their groups'), while a
 * grader sees all, subject to separate-group restrictions.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class vimipad_source implements source_interface {
    /** Reference (model solution) mode. */
    public const MODE_REFERENCE = 'reference';

    /** Learner submissions mode. */
    public const MODE_SUBMISSIONS = 'submissions';

    /** @var int The course module id of the source ViMi Pad activity. */
    protected int $cmid;

    /** @var string The source mode. */
    protected string $mode;

    /**
     * Constructor.
     *
     * @param int $cmid The course module id of the source ViMi Pad activity.
     * @param string $mode One of the MODE_* constants.
     */
    public function __construct(int $cmid, string $mode = self::MODE_SUBMISSIONS) {
        $this->cmid = $cmid;
        $this->mode = $mode;
    }

    /**
     * The visible maps of the source activity for the viewer.
     *
     * @param int|null $userid The viewer, or null for the current user.
     * @return \stdClass[] The visible maps.
     */
    public function get_items(?int $userid = null): array {
        global $DB, $USER;

        $userid = $userid ?? (int) $USER->id;

        $cm = get_coursemodule_from_id('vimipad', $this->cmid, 0, false, IGNORE_MISSING);
        if (!$cm) {
            return [];
        }
        $modinfo = get_fast_modinfo($cm->course, $userid);
        if (!isset($modinfo->cms[$cm->id]) || !$modinfo->cms[$cm->id]->uservisible) {
            return [];
        }
        $context = \context_module::instance($cm->id);
        $cangrade = has_capability('mod/vimipad:grade', $context, $userid);

        if ($this->mode === self::MODE_REFERENCE) {
            // The model solution reveals the answer, so gate it behind grading.
            if (!$cangrade) {
                return [];
            }
            $reference = $DB->get_field('vimipad', 'referencemapjson', ['id' => $cm->instance]);
            return $this->as_items([
                (object) ['mapjson' => $reference, 'authorname' => ''],
            ]);
        }

        return $this->submission_items($cm, $context, $userid, $cangrade);
    }

    /**
     * The submitted snapshots this viewer may see, newest first and bounded.
     *
     * @param \stdClass $cm The source course module.
     * @param \context_module $context The source context.
     * @param int $userid The viewer.
     * @param bool $cangrade Whether the viewer may grade in the source activity.
     * @return array The gallery items.
     */
    protected function submission_items($cm, $context, int $userid, bool $cangrade): array {
        global $DB;

        $params = ['vimipadid' => $cm->instance];
        $where = ['w.vimipadid = :vimipadid', 'w.submittedsnapshotid IS NOT NULL'];

        $clause = $this->visibility_clause($cm, $context, $userid, $cangrade);
        if ($clause !== null) {
            $where[] = $clause->sql;
            $params += $clause->params;
        }

        $sql = "SELECT s.id, s.snapshotjson AS mapjson, w.userid, w.groupid
                  FROM {vimipad_workspace} w
                  JOIN {vimipad_snapshot} s ON s.id = w.submittedsnapshotid
                 WHERE " . implode(' AND ', $where) . "
              ORDER BY s.timecreated DESC, s.id DESC";
        // Bounded and newest-first, then restored to chronological order.
        $rows = array_reverse($DB->get_records_sql($sql, $params, 0, self::MAX_ITEMS), true);

        return $this->as_items($this->as_entries($rows));
    }

    /**
     * The group/ownership restriction that applies to this viewer, if any.
     *
     * Learners see only their own and their groups' submissions; graders see
     * everything unless separate groups restrict them to their own.
     *
     * @param \stdClass $cm The source course module.
     * @param \context_module $context The source context.
     * @param int $userid The viewer.
     * @param bool $cangrade Whether the viewer may grade.
     * @return object|null Object with sql and params, or null for no restriction.
     */
    protected function visibility_clause($cm, $context, int $userid, bool $cangrade) {
        global $DB;

        $mygroups = groups_get_user_groups($cm->course, $userid)[0] ?? [];

        if (!$cangrade) {
            $mine = ['w.userid = :owner'];
            $params = ['owner' => $userid];
            if (!empty($mygroups)) {
                [$ingroup, $groupparams] = $DB->get_in_or_equal($mygroups, SQL_PARAMS_NAMED, 'og');
                $mine[] = "w.groupid $ingroup";
                $params += $groupparams;
            }
            return (object) ['sql' => '(' . implode(' OR ', $mine) . ')', 'params' => $params];
        }

        $separate = groups_get_activity_groupmode($cm) == SEPARATEGROUPS
            && !has_capability('moodle/site:accessallgroups', $context, $userid);
        if (!$separate) {
            return null;
        }
        if (empty($mygroups)) {
            return (object) ['sql' => 'w.groupid IS NULL', 'params' => []];
        }
        [$ingroup, $groupparams] = $DB->get_in_or_equal($mygroups, SQL_PARAMS_NAMED, 'sg');
        return (object) [
            'sql' => "(w.groupid $ingroup OR w.groupid IS NULL)",
            'params' => $groupparams,
        ];
    }

    /**
     * Label each submitted snapshot with its owner, batching the name lookups.
     *
     * @param array $rows The snapshot rows.
     * @return array The entries ready for as_items().
     */
    protected function as_entries(array $rows): array {
        // One query for the users; the group cache is filled once rather than
        // per submitted workspace.
        $names = \mod_vimigallery\local\comment_service::author_names(
            array_map(fn($r) => (int) $r->userid, $rows)
        );
        $groupnames = [];

        $entries = [];
        foreach ($rows as $row) {
            $groupid = (int) $row->groupid;
            if ($groupid > 0 && !array_key_exists($groupid, $groupnames)) {
                $groupnames[$groupid] = (string) groups_get_group_name($groupid);
            }
            $entries[] = (object) [
                'mapjson' => $row->mapjson,
                'authorname' => $groupid > 0 ? $groupnames[$groupid] : ($names[(int) $row->userid] ?? ''),
                'sourceuserid' => $groupid > 0 ? null : (int) $row->userid,
            ];
        }
        return $entries;
    }

    /**
     * Resolve an author label from a workspace owner (user or group).
     *
     * @param int $ownerid The workspace user id (0 if a group workspace).
     * @param int $groupid The workspace group id (0 if an individual workspace).
     * @return string The author label.
     */
    protected function author_name(int $ownerid, int $groupid): string {
        if ($groupid) {
            $name = groups_get_group_name($groupid);
            return $name !== false ? (string) $name : '';
        }
        if ($ownerid) {
            $user = \core_user::get_user($ownerid, '*', IGNORE_MISSING);
            return $user ? fullname($user) : '';
        }
        return '';
    }

    /**
     * Turn raw {mapjson, authorname} rows into normalised, validated items.
     *
     * @param \stdClass[] $entries The raw entries.
     * @return \stdClass[] The valid gallery items in order.
     */
    protected function as_items(array $entries): array {
        $items = [];
        $sortorder = 0;
        foreach ($entries as $entry) {
            if ($entry->mapjson === null || trim((string) $entry->mapjson) === '') {
                continue;
            }
            $decoded = json_decode($entry->mapjson, true);
            if (!is_array($decoded) || !isset($decoded['nodes'])) {
                continue;
            }
            $profile = isset($decoded['profile']) && is_string($decoded['profile'])
                ? $decoded['profile'] : 'conceptmap';

            $item = new \stdClass();
            $item->id = 'vp' . $sortorder;
            $item->mapjson = $entry->mapjson;
            $item->profile = \core_text::substr($profile, 0, 40);
            $item->authorname = \core_text::substr($entry->authorname, 0, 255);
            $item->sortorder = $sortorder++;
            $item->visible = 1;
            $item->sourceuserid = isset($entry->sourceuserid) ? (int) $entry->sourceuserid : null;
            $items[] = $item;
        }
        return $items;
    }
}
