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

    /**
     * Two byte-identical maps keep their own comments across a rebuild.
     *
     * Comments used to be re-linked by content hash, which cannot tell identical
     * maps apart - empty maps, a shared template, identical answers - and could
     * therefore move a comment onto someone else's map.
     *
     * @return void
     */
    public function test_identical_maps_keep_separate_comments(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module(
            'vimigallery',
            ['course' => $course->id, 'allowcomments' => 1]
        );
        $cm = get_coursemodule_from_instance('vimigallery', $module->id);
        $context = \context_module::instance($cm->id);

        // Two uploads with exactly the same content.
        $map = (string) json_encode([
            'profile' => 'conceptmap',
            'nodes' => [['stableid' => 'n1', 'label' => 'Same']],
            'relations' => [],
        ]);
        $fs = get_file_storage();
        $base = [
            'contextid' => $context->id,
            'component' => 'mod_vimigallery',
            'filearea' => 'source',
            'itemid' => 0,
            'filepath' => '/',
        ];
        $fs->create_file_from_string($base + ['filename' => 'one.json'], $map);
        $fs->create_file_from_string($base + ['filename' => 'two.json'], $map);

        vimigallery_rebuild_items($module->id, $context);
        $items = array_values($DB->get_records('vimigallery_item', ['galleryid' => $module->id], 'sourcekey ASC'));
        $this->assertCount(2, $items);
        $this->assertNotSame($items[0]->sourcekey, $items[1]->sourcekey);

        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
        comment_service::post($cm, (int) $items[0]->id, (int) $user->id, 'on one');
        comment_service::post($cm, (int) $items[1]->id, (int) $user->id, 'on two');

        // A rebuild must leave each comment on its own map.
        vimigallery_rebuild_items($module->id, $context);
        $after = $DB->get_records('vimigallery_item', ['galleryid' => $module->id], 'sourcekey ASC');
        $bykey = [];
        foreach ($after as $item) {
            $bykey[$item->sourcekey] = (int) $item->id;
        }
        $comments = $DB->get_records('vimigallery_comment', ['galleryid' => $module->id]);
        $this->assertCount(2, $comments);
        $placed = [];
        foreach ($comments as $comment) {
            $placed[$comment->content] = (int) $comment->itemid;
        }
        $this->assertSame($bykey[$items[0]->sourcekey], $placed['on one']);
        $this->assertSame($bykey[$items[1]->sourcekey], $placed['on two']);
        $this->assertNotSame($placed['on one'], $placed['on two']);
    }

    /**
     * A hidden item cannot collect comments, even when its id is known.
     *
     * @return void
     */
    public function test_hidden_item_refuses_comments(): void {
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
            'visible' => 0,
            'sourcetype' => 'upload',
            'profile' => 'conceptmap',
            'mapjson' => $mapjson,
            'authorname' => '',
            'contenthash' => sha1($mapjson),
            'sourceuserid' => null,
            'sourcekey' => 'upload:hidden.json',
            'timecreated' => time(),
        ]);
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->expectException(\moodle_exception::class);
        comment_service::post($cm, $itemid, (int) $user->id, 'should not stick');
    }

    /**
     * The viewed event can actually be instantiated and triggered.
     *
     * \core\event\course_module_viewed is abstract, so triggering it directly
     * threw on every page view - a defect no unit test noticed because none of
     * them loaded view.php.
     *
     * @return void
     */
    public function test_viewed_event_can_be_triggered(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('vimigallery', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vimigallery', $module->id);
        $context = \context_module::instance($cm->id);

        $sink = $this->redirectEvents();
        $event = \mod_vimigallery\event\course_module_viewed::create([
            'objectid' => $module->id,
            'context' => $context,
        ]);
        $event->trigger();
        $events = $sink->get_events();
        $sink->close();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(\mod_vimigallery\event\course_module_viewed::class, $events[0]);
        $this->assertEquals($context->id, $events[0]->contextid);
        $this->assertEquals($module->id, $events[0]->objectid);
    }
}
