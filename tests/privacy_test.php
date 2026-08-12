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

namespace mod_vimigallery;

use mod_vimigallery\local\comment_service;
use mod_vimigallery\privacy\provider;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\writer;

/**
 * Tests for the mod_vimigallery privacy provider.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_vimigallery\privacy\provider
 */
final class privacy_test extends \core_privacy\tests\provider_testcase {
    /**
     * Build a gallery with one commentable item.
     *
     * @return array [context, cm (stdClass), itemid]
     */
    private function make(): array {
        global $DB;
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module(
            'vimigallery',
            ['course' => $course->id, 'allowcomments' => 1]
        );
        $cm = get_coursemodule_from_instance('vimigallery', $module->id);
        $itemid = (int) $DB->insert_record('vimigallery_item', (object) [
            'galleryid' => $module->id,
            'sortorder' => 0,
            'visible' => 1,
            'sourcetype' => 'upload',
            'profile' => 'conceptmap',
            'mapjson' => '{"nodes":[]}',
            'authorname' => '',
            'contenthash' => sha1('x'),
            'timecreated' => time(),
        ]);
        return [\context_module::instance($cm->id), $cm, $itemid];
    }

    /**
     * A user's comments are exported for the gallery context.
     *
     * @return void
     */
    public function test_export(): void {
        $this->resetAfterTest();
        [$context, $cm, $itemid] = $this->make();
        $user = $this->getDataGenerator()->create_user();
        comment_service::post($cm, $itemid, (int) $user->id, 'Exported comment');

        $this->export_context_data_for_user((int) $user->id, $context, 'mod_vimigallery');
        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
    }

    /**
     * Deleting for a user removes only their comments.
     *
     * @return void
     */
    public function test_delete_for_user(): void {
        global $DB;
        $this->resetAfterTest();
        [$context, $cm, $itemid] = $this->make();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        comment_service::post($cm, $itemid, (int) $user1->id, 'u1');
        comment_service::post($cm, $itemid, (int) $user2->id, 'u2');

        $contextlist = new approved_contextlist($user1, 'mod_vimigallery', [$context->id]);
        provider::delete_data_for_user($contextlist);

        $this->assertEquals(0, $DB->count_records('vimigallery_comment', ['userid' => $user1->id]));
        $this->assertEquals(1, $DB->count_records('vimigallery_comment', ['userid' => $user2->id]));
    }

    /**
     * Deleting for all users in a context clears every comment.
     *
     * @return void
     */
    public function test_delete_all(): void {
        global $DB;
        $this->resetAfterTest();
        [$context, $cm, $itemid] = $this->make();
        $user = $this->getDataGenerator()->create_user();
        comment_service::post($cm, $itemid, (int) $user->id, 'gone');

        provider::delete_data_for_all_users_in_context($context);
        $this->assertEquals(0, $DB->count_records('vimigallery_comment', ['galleryid' => $cm->instance]));
    }
}
