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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/vimigallery/lib.php');

/**
 * Tests for the mod_vimigallery library.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \vimigallery_rebuild_items
 */
final class lib_test extends \advanced_testcase {
    /**
     * Supported features are declared as expected.
     *
     * @return void
     */
    public function test_supports(): void {
        $this->assertTrue(vimigallery_supports(FEATURE_MOD_INTRO));
        $this->assertTrue(vimigallery_supports(FEATURE_BACKUP_MOODLE2));
        $this->assertFalse(vimigallery_supports(FEATURE_GRADE_HAS_GRADE));
        $this->assertNull(vimigallery_supports('some_unknown_feature'));
    }

    /**
     * A gallery instance can be created with defaults and then deleted.
     *
     * @return void
     */
    public function test_create_and_delete_instance(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('vimigallery', ['course' => $course->id]);

        $instance = $DB->get_record('vimigallery', ['id' => $module->id], '*', MUST_EXIST);
        $this->assertSame('page', $instance->displaymode);
        $this->assertEquals(1, $instance->showtabs);

        vimigallery_delete_instance($module->id);
        $this->assertFalse($DB->record_exists('vimigallery', ['id' => $module->id]));
    }

    /**
     * Uploaded JSON files become frozen gallery items; junk files are skipped.
     *
     * @return void
     */
    public function test_rebuild_items_from_files(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('vimigallery', ['course' => $course->id]);
        $context = \context_module::instance($module->cmid);

        $fs = get_file_storage();
        $base = [
            'contextid' => $context->id,
            'component' => 'mod_vimigallery',
            'filearea' => 'source',
            'itemid' => 0,
            'filepath' => '/',
        ];
        $map = json_encode([
            'profile' => 'mindmap',
            'author' => 'Ada Lovelace',
            'nodes' => [['stableid' => 'a', 'label' => 'Root']],
            'relations' => [],
        ]);
        $fs->create_file_from_string(['filename' => 'map1.json'] + $base, $map);
        // A non-map file must be ignored.
        $fs->create_file_from_string(['filename' => 'junk.json'] + $base, '{"nope":1}');

        vimigallery_rebuild_items($module->id, $context);

        $items = array_values($DB->get_records('vimigallery_item', ['galleryid' => $module->id], 'sortorder ASC'));
        $this->assertCount(1, $items);
        $this->assertSame('mindmap', $items[0]->profile);
        $this->assertSame('Ada Lovelace', $items[0]->authorname);
        $this->assertSame('upload', $items[0]->sourcetype);
        $this->assertStringContainsString('Root', $items[0]->mapjson);
    }

    /**
     * The renderer produces an album with one slide per item and, for more than
     * one item, prev/next controls and a counter.
     *
     * @return void
     */
    public function test_renderer_album_markup(): void {
        global $DB, $PAGE;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module(
            'vimigallery',
            ['course' => $course->id, 'showauthors' => 1]
        );

        $map = '{"profile":"conceptmap","nodes":[{"stableid":"a","label":"Root"}],"relations":[]}';
        foreach ([0, 1] as $i) {
            $DB->insert_record('vimigallery_item', (object) [
                'galleryid' => $module->id,
                'sortorder' => $i,
                'visible' => 1,
                'sourcetype' => 'upload',
                'profile' => 'conceptmap',
                'mapjson' => $map,
                'authorname' => 'Author ' . $i,
                'timecreated' => time(),
            ]);
        }

        $cm = get_fast_modinfo($course)->get_cm($module->cmid);
        $context = \context_module::instance($module->cmid);
        $PAGE->set_url('/mod/vimigallery/view.php', ['id' => $module->cmid]);
        $PAGE->set_context($context);

        $renderable = new \mod_vimigallery\output\gallery($cm);
        $html = $PAGE->get_renderer('mod_vimigallery')->render($renderable);

        // Two slides, prev/next controls, an "x / 2" counter and the author names.
        $this->assertSame(2, substr_count($html, 'vimigallery-slide'));
        $this->assertStringContainsString('data-vimigallery-prev', $html);
        $this->assertStringContainsString('data-vimigallery-next', $html);
        $this->assertStringContainsString('/ 2', $html);
        $this->assertStringContainsString('Author 0', $html);

        // With a single visible item there are no navigation controls.
        $DB->set_field('vimigallery_item', 'visible', 0, ['galleryid' => $module->id, 'sortorder' => 1]);
        $html = $PAGE->get_renderer('mod_vimigallery')->render(new \mod_vimigallery\output\gallery($cm));
        $this->assertSame(1, substr_count($html, 'vimigallery-slide'));
        $this->assertStringNotContainsString('data-vimigallery-prev', $html);
    }
}
