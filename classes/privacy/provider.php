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

namespace mod_vimigallery\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy provider for mod_vimigallery: covers learner comments on maps.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {
    /**
     * Describe the personal data stored by this plugin.
     *
     * @param collection $collection The metadata collection to add to.
     * @return collection The updated collection.
     */
    public static function get_metadata(collection $collection): collection {
        // Materialised items can hold learner-derived content: when a gallery
        // pulls maps from a quiz, database or ViMi Pad activity it stores a copy
        // of the map and the author's display name. Ownership of the originals
        // stays with the source activity, but these copies must be declared.
        $collection->add_database_table('vimigallery_item', [
            'mapjson' => 'privacy:metadata:vimigallery_item:mapjson',
            'authorname' => 'privacy:metadata:vimigallery_item:authorname',
            'sourceuserid' => 'privacy:metadata:vimigallery_item:sourceuserid',
        ], 'privacy:metadata:vimigallery_item');

        // A materialised group map is joint work, so it records everyone who
        // contributed rather than a single owner.
        $collection->add_database_table('vimigallery_item_user', [
            'userid' => 'privacy:metadata:vimigallery_item_user:userid',
        ], 'privacy:metadata:vimigallery_item_user');

        $collection->add_database_table('vimigallery_comment', [
            'userid' => 'privacy:metadata:vimigallery_comment:userid',
            'content' => 'privacy:metadata:vimigallery_comment:content',
            'timecreated' => 'privacy:metadata:vimigallery_comment:timecreated',
        ], 'privacy:metadata:vimigallery_comment');
        return $collection;
    }

    /**
     * Contexts where the user has data.
     *
     * @param int $userid The user id.
     * @return contextlist The contexts.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();
        $sql = "SELECT ctx.id
                  FROM {vimigallery_comment} c
                  JOIN {vimigallery} g ON g.id = c.galleryid
                  JOIN {course_modules} cm ON cm.instance = g.id
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :modlevel
                 WHERE c.userid = :userid";
        $contextlist->add_from_sql($sql, [
            'modname' => 'vimigallery',
            'modlevel' => CONTEXT_MODULE,
            'userid' => $userid,
        ]);

        // Galleries holding a materialised copy of this user's work.
        $itemsql = "SELECT ctx.id
                      FROM {vimigallery_item} i
                      JOIN {vimigallery} g ON g.id = i.galleryid
                      JOIN {course_modules} cm ON cm.instance = g.id
                      JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                      JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :modlevel
                     WHERE i.sourceuserid = :userid";
        $contextlist->add_from_sql($itemsql, [
            'modname' => 'vimigallery',
            'modlevel' => CONTEXT_MODULE,
            'userid' => $userid,
        ]);

        // Galleries holding a group map this user contributed to.
        $groupsql = "SELECT ctx.id
                       FROM {vimigallery_item_user} iu
                       JOIN {vimigallery_item} i ON i.id = iu.itemid
                       JOIN {vimigallery} g ON g.id = i.galleryid
                       JOIN {course_modules} cm ON cm.instance = g.id
                       JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                       JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :modlevel
                      WHERE iu.userid = :userid";
        $contextlist->add_from_sql($groupsql, [
            'modname' => 'vimigallery',
            'modlevel' => CONTEXT_MODULE,
            'userid' => $userid,
        ]);
        return $contextlist;
    }

    /**
     * Users with data in a context.
     *
     * @param userlist $userlist The userlist to populate.
     * @return void
     */
    public static function get_users_in_context(userlist $userlist): void {
        $context = $userlist->get_context();
        if (!$context instanceof \context_module) {
            return;
        }
        $sql = "SELECT c.userid
                  FROM {vimigallery_comment} c
                  JOIN {vimigallery} g ON g.id = c.galleryid
                  JOIN {course_modules} cm ON cm.instance = g.id
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                 WHERE cm.id = :cmid";
        $userlist->add_from_sql('userid', $sql, [
            'modname' => 'vimigallery',
            'cmid' => $context->instanceid,
        ]);

        $itemsql = "SELECT i.sourceuserid AS userid
                      FROM {vimigallery_item} i
                      JOIN {vimigallery} g ON g.id = i.galleryid
                      JOIN {course_modules} cm ON cm.instance = g.id
                      JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                     WHERE cm.id = :cmid AND i.sourceuserid IS NOT NULL";
        $userlist->add_from_sql('userid', $itemsql, [
            'modname' => 'vimigallery',
            'cmid' => $context->instanceid,
        ]);

        $groupsql = "SELECT iu.userid
                       FROM {vimigallery_item_user} iu
                       JOIN {vimigallery_item} i ON i.id = iu.itemid
                       JOIN {vimigallery} g ON g.id = i.galleryid
                       JOIN {course_modules} cm ON cm.instance = g.id
                       JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                      WHERE cm.id = :cmid";
        $userlist->add_from_sql('userid', $groupsql, [
            'modname' => 'vimigallery',
            'cmid' => $context->instanceid,
        ]);
    }

    /**
     * Export a user's comments in the approved contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts.
     * @return void
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_module) {
                continue;
            }
            $cm = get_coursemodule_from_id('vimigallery', $context->instanceid);
            if (!$cm) {
                continue;
            }
            $comments = $DB->get_records('vimigallery_comment', [
                'galleryid' => $cm->instance,
                'userid' => $userid,
            ], 'timecreated ASC');
            if (empty($comments)) {
                continue;
            }
            $data = [];
            foreach ($comments as $comment) {
                $data[] = (object) [
                    'content' => $comment->content,
                    'timecreated' => \core_privacy\local\request\transform::datetime($comment->timecreated),
                ];
            }
            writer::with_context($context)->export_data(
                [get_string('comments', 'mod_vimigallery')],
                (object) ['comments' => $data]
            );
        }

        self::export_items($contextlist);
    }

    /**
     * Export the materialised copies of a user's maps.
     *
     * @param approved_contextlist $contextlist The approved contexts.
     * @return void
     */
    protected static function export_items(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_module) {
                continue;
            }
            $cm = get_coursemodule_from_id('vimigallery', $context->instanceid);
            if (!$cm) {
                continue;
            }
            $items = $DB->get_records_sql(
                "SELECT DISTINCT i.*
                   FROM {vimigallery_item} i
              LEFT JOIN {vimigallery_item_user} iu ON iu.itemid = i.id AND iu.userid = :contributor
                  WHERE i.galleryid = :galleryid
                        AND (i.sourceuserid = :owner OR iu.userid IS NOT NULL)
               ORDER BY i.sortorder ASC",
                ['galleryid' => $cm->instance, 'owner' => $userid, 'contributor' => $userid]
            );
            if (empty($items)) {
                continue;
            }
            $data = [];
            foreach ($items as $item) {
                $data[] = (object) [
                    'profile' => $item->profile,
                    'authorname' => $item->authorname,
                    'mapjson' => $item->mapjson,
                ];
            }
            writer::with_context($context)->export_data(
                [get_string('pluginname', 'mod_vimigallery')],
                (object) ['maps' => $data]
            );
        }
    }

    /**
     * Delete all comments in a context.
     *
     * @param \context $context The context.
     * @return void
     */
    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;
        if (!$context instanceof \context_module) {
            return;
        }
        $cm = get_coursemodule_from_id('vimigallery', $context->instanceid);
        if ($cm) {
            $DB->delete_records('vimigallery_comment', ['galleryid' => $cm->instance]);
            // Materialised copies of learners' work are derived data: the source
            // activity remains the authoritative record, so the copy is removed
            // rather than anonymised.
            $DB->delete_records_select(
                'vimigallery_item',
                'galleryid = :galleryid AND sourceuserid IS NOT NULL',
                ['galleryid' => $cm->instance]
            );
            $itemids = $DB->get_fieldset_select('vimigallery_item', 'id', 'galleryid = ?', [$cm->instance]);
            if (!empty($itemids)) {
                [$insql, $inparams] = $DB->get_in_or_equal($itemids);
                $DB->delete_records_select('vimigallery_item_user', "itemid $insql", $inparams);
            }
        }
    }

    /**
     * Delete a user's comments in the approved contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts.
     * @return void
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_module) {
                continue;
            }
            $cm = get_coursemodule_from_id('vimigallery', $context->instanceid);
            if ($cm) {
                $DB->delete_records('vimigallery_comment', [
                    'galleryid' => $cm->instance,
                    'userid' => $userid,
                ]);
                // An individual map is this learner's own work, so the copy goes.
                $DB->delete_records('vimigallery_item', [
                    'galleryid' => $cm->instance,
                    'sourceuserid' => $userid,
                ]);
                // A group map is not: it also holds other people's work, so the
                // map and its group label stay and only the personal link is
                // removed. That mirrors how mod_vimipad anonymises shared
                // contributions rather than deleting them.
                self::unlink_contributions($cm->instance, [$userid]);
            }
        }
    }

    /**
     * Remove users from the contributor list of this gallery's group maps.
     *
     * The maps themselves are left untouched: a frozen group map contains other
     * people's work, and its author label is the group, not a person. Removing
     * the link is what anonymises the contribution.
     *
     * @param int $galleryid The gallery instance id.
     * @param array $userids The users to unlink.
     * @return void
     */
    protected static function unlink_contributions(int $galleryid, array $userids): void {
        global $DB;

        $userids = array_values(array_filter(array_map('intval', $userids)));
        if (empty($userids)) {
            return;
        }
        $itemids = $DB->get_fieldset_select('vimigallery_item', 'id', 'galleryid = ?', [$galleryid]);
        if (empty($itemids)) {
            return;
        }
        [$initem, $itemparams] = $DB->get_in_or_equal($itemids, SQL_PARAMS_NAMED, 'it');
        [$inuser, $userparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'us');
        $DB->delete_records_select(
            'vimigallery_item_user',
            "itemid $initem AND userid $inuser",
            array_merge($itemparams, $userparams)
        );
    }

    /**
     * Delete the given users' comments in a context.
     *
     * @param approved_userlist $userlist The approved users.
     * @return void
     */
    public static function delete_data_for_users(approved_userlist $userlist): void {
        global $DB;
        $context = $userlist->get_context();
        if (!$context instanceof \context_module) {
            return;
        }
        $cm = get_coursemodule_from_id('vimigallery', $context->instanceid);
        if (!$cm) {
            return;
        }
        $userids = $userlist->get_userids();
        if (empty($userids)) {
            return;
        }
        [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params['galleryid'] = $cm->instance;
        $DB->delete_records_select('vimigallery_comment', "galleryid = :galleryid AND userid $insql", $params);
        $DB->delete_records_select('vimigallery_item', "galleryid = :galleryid AND sourceuserid $insql", $params);
        self::unlink_contributions((int) $cm->instance, $userids);
    }
}
