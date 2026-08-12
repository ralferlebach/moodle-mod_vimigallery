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

namespace mod_vimigallery\local;

/**
 * Learner comments on individual maps.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class comment_service {
    /** @var int Maximum stored comment length. */
    public const MAX_LENGTH = 20000;

    /**
     * Store a comment on a map and refresh completion for the author.
     *
     * @param \stdClass $cm The gallery course module.
     * @param int $itemid The map (vimigallery_item) id.
     * @param int $userid The author.
     * @param string $content The comment text.
     * @return \stdClass The stored comment record.
     */
    public static function post(\stdClass $cm, int $itemid, int $userid, string $content): \stdClass {
        global $DB;

        if (!$DB->record_exists('vimigallery_item', ['id' => $itemid, 'galleryid' => $cm->instance])) {
            throw new \moodle_exception('invaliditem', 'mod_vimigallery');
        }
        $content = \core_text::substr(trim($content), 0, self::MAX_LENGTH);
        if ($content === '') {
            throw new \moodle_exception('emptycomment', 'mod_vimigallery');
        }

        $now = time();
        $record = (object) [
            'galleryid' => (int) $cm->instance,
            'itemid' => $itemid,
            'userid' => $userid,
            'content' => $content,
            'format' => FORMAT_PLAIN,
            'timecreated' => $now,
            'timemodified' => $now,
        ];
        $record->id = $DB->insert_record('vimigallery_comment', $record);

        // Refresh completion for the author.
        $course = get_course($cm->course);
        $completion = new \completion_info($course);
        if ($completion->is_enabled($cm)) {
            $completion->update_state($cm, COMPLETION_UNKNOWN, $userid);
        }

        return $record;
    }

    /**
     * The comments on a map, oldest first, with author display names.
     *
     * @param int $itemid The map id.
     * @return \stdClass[] Comments, each with authorname added.
     */
    public static function get_for_item(int $itemid): array {
        global $DB;

        $comments = $DB->get_records('vimigallery_comment', ['itemid' => $itemid], 'timecreated ASC, id ASC');
        if (empty($comments)) {
            return [];
        }
        $names = self::author_names(array_map(fn($c) => (int) $c->userid, $comments));
        foreach ($comments as $comment) {
            $comment->authorname = $names[(int) $comment->userid] ?? '';
        }
        return array_values($comments);
    }

    /**
     * All comments of a gallery, grouped by item id.
     *
     * Loading them per item would cost two queries per slide (comments plus the
     * authors), so a gallery page would scale with the number of maps. This does
     * it in two queries for the whole page.
     *
     * @param int $galleryid The gallery instance id.
     * @return array Map of itemid => list of comments (each with authorname).
     */
    public static function get_for_gallery(int $galleryid): array {
        global $DB;

        $comments = $DB->get_records('vimigallery_comment', ['galleryid' => $galleryid], 'timecreated ASC, id ASC');
        if (empty($comments)) {
            return [];
        }
        $names = self::author_names(array_map(fn($c) => (int) $c->userid, $comments));

        $grouped = [];
        foreach ($comments as $comment) {
            $comment->authorname = $names[(int) $comment->userid] ?? '';
            $grouped[(int) $comment->itemid][] = $comment;
        }
        return $grouped;
    }

    /**
     * Display names for a set of user ids, in one query.
     *
     * @param array $userids The user ids (may repeat).
     * @return array Map of userid => full name.
     */
    public static function author_names(array $userids): array {
        global $DB;

        $userids = array_values(array_unique(array_filter(array_map('intval', $userids))));
        if (empty($userids)) {
            return [];
        }
        [$insql, $params] = $DB->get_in_or_equal($userids);
        $users = $DB->get_records_select('user', "id $insql", $params);

        $names = [];
        foreach ($users as $user) {
            $names[(int) $user->id] = fullname($user);
        }
        return $names;
    }

    /**
     * The number of comments a user has posted in a gallery.
     *
     * @param int $galleryid The gallery instance id.
     * @param int $userid The user.
     * @return int The comment count.
     */
    public static function count_for_user(int $galleryid, int $userid): int {
        global $DB;
        return $DB->count_records('vimigallery_comment', ['galleryid' => $galleryid, 'userid' => $userid]);
    }
}
