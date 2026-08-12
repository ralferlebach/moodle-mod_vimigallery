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
use mod_vimigallery\completion\custom_completion;

/**
 * Tests for gallery comments and the comment completion rule.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_vimigallery\local\comment_service
 */
final class comment_test extends \advanced_testcase {
    /**
     * Create a gallery with one item and return context data.
     *
     * @param array $extra Extra module settings.
     * @return array [course, cm (stdClass), itemid]
     */
    private function make(array $extra = []): array {
        global $DB;
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $module = $this->getDataGenerator()->create_module(
            'vimigallery',
            ['course' => $course->id, 'allowcomments' => 1] + $extra
        );
        $cm = get_coursemodule_from_instance('vimigallery', $module->id);
        $itemid = (int) $DB->insert_record('vimigallery_item', (object) [
            'galleryid' => $module->id,
            'sortorder' => 0,
            'visible' => 1,
            'sourcetype' => 'upload',
            'profile' => 'conceptmap',
            'mapjson' => '{"profile":"conceptmap","nodes":[],"relations":[]}',
            'authorname' => '',
            'contenthash' => sha1('x'),
            'timecreated' => time(),
        ]);
        return [$course, $cm, $itemid];
    }

    /**
     * Posting stores the comment and it can be listed and counted.
     *
     * @return void
     */
    public function test_post_list_count(): void {
        $this->resetAfterTest();
        [$course, $cm, $itemid] = $this->make();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        comment_service::post($cm, $itemid, (int) $user->id, '  Hello map  ');

        $comments = comment_service::get_for_item($itemid);
        $this->assertCount(1, $comments);
        $this->assertSame('Hello map', $comments[0]->content);
        $this->assertSame(1, comment_service::count_for_user($cm->instance, (int) $user->id));
    }

    /**
     * Posting to an item of another gallery is rejected.
     *
     * @return void
     */
    public function test_post_invalid_item(): void {
        $this->resetAfterTest();
        [, $cm] = $this->make();
        $user = $this->getDataGenerator()->create_user();

        $this->expectException(\moodle_exception::class);
        comment_service::post($cm, -1, (int) $user->id, 'x');
    }

    /**
     * The web service enforces the comment capability.
     *
     * @return void
     */
    public function test_post_comment_external(): void {
        $this->resetAfterTest();
        [$course, $cm, $itemid] = $this->make();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($student);
        $result = \mod_vimigallery\external\post_comment::execute($cm->id, $itemid, 'Nice');
        $this->assertSame('Nice', $result['content']);

        // A user who cannot access the module is refused.
        $outsider = $this->getDataGenerator()->create_user();
        $this->setUser($outsider);
        $this->expectException(\moodle_exception::class);
        \mod_vimigallery\external\post_comment::execute($cm->id, $itemid, 'Blocked');
    }

    /**
     * The completion rule flips once the minimum comment count is reached.
     *
     * @return void
     */
    public function test_completion_rule(): void {
        $this->resetAfterTest();
        [$course, $cm, $itemid] = $this->make([
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'completioncommentsmin' => 2,
        ]);
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $cminfo = get_fast_modinfo($course, $user->id)->get_cm($cm->id);

        $completion = new custom_completion($cminfo, (int) $user->id);
        $this->assertSame(COMPLETION_INCOMPLETE, $completion->get_state('completioncommentsmin'));

        comment_service::post($cm, $itemid, (int) $user->id, 'one');
        $completion = new custom_completion($cminfo, (int) $user->id);
        $this->assertSame(COMPLETION_INCOMPLETE, $completion->get_state('completioncommentsmin'));

        comment_service::post($cm, $itemid, (int) $user->id, 'two');
        $completion = new custom_completion($cminfo, (int) $user->id);
        $this->assertSame(COMPLETION_COMPLETE, $completion->get_state('completioncommentsmin'));
    }
}
