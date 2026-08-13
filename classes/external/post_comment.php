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

namespace mod_vimigallery\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * External function to post a comment on a map.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post_comment extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters The parameter definition.
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'The gallery course module id'),
            'itemid' => new external_value(PARAM_INT, 'The map item id'),
            'content' => new external_value(PARAM_TEXT, 'The comment text'),
        ]);
    }

    /**
     * Post the comment.
     *
     * @param int $cmid The gallery course module id.
     * @param int $itemid The map item id.
     * @param string $content The comment text.
     * @return array The stored comment for display.
     */
    public static function execute(int $cmid, int $itemid, string $content): array {
        global $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            'cmid' => $cmid,
            'itemid' => $itemid,
            'content' => $content,
        ]);

        $cm = get_coursemodule_from_id('vimigallery', $params['cmid'], 0, false, MUST_EXIST);
        $context = \context_module::instance($cm->id);
        self::validate_context($context);
        // Commenting implies reading: a role that can comment but not view must
        // not be able to reach an item through this path.
        require_capability('mod/vimigallery:view', $context);
        require_capability('mod/vimigallery:comment', $context);

        global $DB;
        $instance = $DB->get_record('vimigallery', ['id' => $cm->instance], '*', MUST_EXIST);
        if (empty($instance->allowcomments)) {
            throw new \moodle_exception('commentsdisabled', 'mod_vimigallery');
        }

        $comment = \mod_vimigallery\local\comment_service::post(
            $cm,
            $params['itemid'],
            (int) $USER->id,
            $params['content']
        );

        return [
            'id' => (int) $comment->id,
            'authorname' => fullname($USER),
            'content' => $comment->content,
            'timecreated' => (int) $comment->timecreated,
        ];
    }

    /**
     * Return value.
     *
     * @return external_single_structure The return definition.
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'The new comment id'),
            'authorname' => new external_value(PARAM_TEXT, 'The author display name'),
            'content' => new external_value(PARAM_TEXT, 'The comment text'),
            'timecreated' => new external_value(PARAM_INT, 'The creation time'),
        ]);
    }
}
