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

/**
 * Regression tests for the gallery hardening fixes.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     ::vimigallery_delete_instance
 * @covers     ::vimigallery_validate_source_selection
 */
final class hardening_test extends \advanced_testcase {
    /**
     * Deleting a gallery removes its comments as well as its items, so no
     * orphaned user data survives and a database enforcing the foreign keys
     * does not refuse the delete.
     *
     * @return void
     */
    public function test_delete_instance_removes_comments(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module(
            'vimigallery',
            ['course' => $course->id, 'allowcomments' => 1]
        );
        $cm = get_coursemodule_from_instance('vimigallery', $module->id);
        $mapjson = '{"profile":"conceptmap","nodes":[],"relations":[]}';
        $itemid = (int) $DB->insert_record('vimigallery_item', (object) [
            'galleryid' => $module->id,
            'sortorder' => 0,
            'visible' => 1,
            'sourcetype' => 'upload',
            'profile' => 'conceptmap',
            'mapjson' => $mapjson,
            'authorname' => '',
            'contenthash' => sha1($mapjson),
            'timecreated' => time(),
        ]);
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
        comment_service::post($cm, $itemid, (int) $user->id, 'Hello');

        $this->assertEquals(1, $DB->count_records('vimigallery_comment', ['galleryid' => $module->id]));

        vimigallery_delete_instance($module->id);

        $this->assertEquals(0, $DB->count_records('vimigallery_comment', ['galleryid' => $module->id]));
        $this->assertEquals(0, $DB->count_records('vimigallery_item', ['galleryid' => $module->id]));
        $this->assertEquals(0, $DB->count_records('vimigallery', ['id' => $module->id]));
    }

    /**
     * A source pointing at an activity in a different course is refused, even
     * though the posted id is a real course module. The gallery falls back to an
     * upload gallery rather than materialising another course's content.
     *
     * @return void
     */
    public function test_cross_course_source_is_refused(): void {
        $this->resetAfterTest();

        $coursea = $this->getDataGenerator()->create_course();
        $courseb = $this->getDataGenerator()->create_course();
        $foreign = $this->getDataGenerator()->create_module('vimipad', ['course' => $courseb->id]);

        $data = (object) [
            'course' => $coursea->id,
            'sourcetype' => 'vimipad',
            'vimipadsource' => $foreign->cmid,
            'freshness' => 'live',
            'sourcemode' => 'reference',
        ];
        vimigallery_prepare_source_fields($data);

        $this->assertSame('upload', $data->sourcetype);
        $this->assertSame(0, (int) $data->sourcecmid);
    }

    /**
     * A source in the gallery's own course is accepted.
     *
     * @return void
     */
    public function test_same_course_source_is_accepted(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $source = $this->getDataGenerator()->create_module('vimipad', ['course' => $course->id]);

        $data = (object) [
            'course' => $course->id,
            'sourcetype' => 'vimipad',
            'vimipadsource' => $source->cmid,
            'freshness' => 'live',
            'sourcemode' => 'reference',
        ];
        vimigallery_prepare_source_fields($data);

        $this->assertSame('vimipad', $data->sourcetype);
        $this->assertSame((int) $source->cmid, (int) $data->sourcecmid);
    }

    /**
     * A source id that names a module of the wrong type is refused.
     *
     * @return void
     */
    public function test_wrong_module_type_is_refused(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $notaquiz = $this->getDataGenerator()->create_module('vimipad', ['course' => $course->id]);

        $data = (object) [
            'course' => $course->id,
            'sourcetype' => 'qtype',
            'qtypesource' => $notaquiz->cmid,
            'freshness' => 'live',
            'sourcemode' => 'reference',
        ];
        vimigallery_prepare_source_fields($data);

        $this->assertSame('upload', $data->sourcetype);
        $this->assertSame(0, (int) $data->sourcecmid);
    }

    /**
     * An upload whose content is not a valid ViMi Pad map is not materialised.
     *
     * @return void
     */
    public function test_rebuild_skips_invalid_uploads(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('vimigallery', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vimigallery', $module->id);
        $context = \context_module::instance($cm->id);

        $fs = get_file_storage();
        $base = [
            'contextid' => $context->id,
            'component' => 'mod_vimigallery',
            'filearea' => 'source',
            'itemid' => 0,
            'filepath' => '/',
        ];
        // Valid map.
        $valid = (string) json_encode([
            'profile' => 'conceptmap',
            'nodes' => [['stableid' => 'n1', 'label' => 'Cat']],
            'relations' => [],
        ]);
        $fs->create_file_from_string($base + ['filename' => 'good.json'], $valid);
        // Parses as JSON but is not a map document.
        $fs->create_file_from_string($base + ['filename' => 'bad.json'], '{"nodes":[{}]}');

        vimigallery_rebuild_items($module->id, $context);

        $items = $DB->get_records('vimigallery_item', ['galleryid' => $module->id]);
        $this->assertCount(1, $items);
        $item = reset($items);
        $this->assertSame(sha1($valid), $item->contenthash);
    }
}
