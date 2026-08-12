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
            }
        }
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
    }
}
