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

use mod_vimigallery\external\get_item;

/**
 * Tests for lazy loading of gallery maps.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_vimigallery\external\get_item
 */
final class lazyload_test extends \advanced_testcase {
    /**
     * Build a gallery with three maps.
     *
     * @return array [course, cm (stdClass), array of item ids, array of map json]
     */
    private function make(): array {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('vimigallery', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vimigallery', $module->id);

        $ids = [];
        $maps = [];
        foreach (['Cat', 'Dog', 'Bird'] as $i => $label) {
            $map = (string) json_encode([
                'profile' => 'conceptmap',
                'nodes' => [['stableid' => 'n1', 'label' => $label]],
                'relations' => [],
            ]);
            $maps[] = $map;
            $ids[] = (string) $DB->insert_record('vimigallery_item', (object) [
                'galleryid' => $module->id,
                'sortorder' => $i,
                'visible' => 1,
                'sourcetype' => 'upload',
                'profile' => 'conceptmap',
                'mapjson' => $map,
                'authorname' => '',
                'contenthash' => sha1($map),
                'sourceuserid' => null,
                'timecreated' => time(),
            ]);
        }
        return [$course, $cm, $ids, $maps];
    }

    /**
     * The web service returns the requested map.
     *
     * @return void
     */
    public function test_returns_requested_map(): void {
        $this->resetAfterTest();
        [$course, $cm, $ids, $maps] = $this->make();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($student);

        $result = get_item::execute($cm->id, $ids[2]);
        $result = \core_external\external_api::clean_returnvalue(get_item::execute_returns(), $result);

        $this->assertSame($maps[2], $result['mapjson']);
        $this->assertSame('conceptmap', $result['profile']);
        $this->assertSame($ids[2], $result['itemid']);
    }

    /**
     * An item belonging to another gallery cannot be fetched through this one.
     *
     * @return void
     */
    public function test_foreign_item_is_refused(): void {
        $this->resetAfterTest();
        [$course, $cm] = $this->make();
        [, , $otherids] = $this->make();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($student);

        $this->expectException(\moodle_exception::class);
        get_item::execute($cm->id, $otherids[0]);
    }

    /**
     * A hidden item is not served, matching what the page would show.
     *
     * @return void
     */
    public function test_hidden_item_is_refused(): void {
        global $DB;
        $this->resetAfterTest();
        [$course, $cm, $ids] = $this->make();
        $DB->set_field('vimigallery_item', 'visible', 0, ['id' => $ids[1]]);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($student);

        $this->expectException(\moodle_exception::class);
        get_item::execute($cm->id, $ids[1]);
    }

    /**
     * A user who cannot reach the module cannot fetch its maps.
     *
     * @return void
     */
    public function test_outsider_is_refused(): void {
        $this->resetAfterTest();
        [, $cm, $ids] = $this->make();
        $outsider = $this->getDataGenerator()->create_user();
        $this->setUser($outsider);

        $this->expectException(\moodle_exception::class);
        get_item::execute($cm->id, $ids[0]);
    }

    /**
     * The page carries only the first map: the others are fetched on demand, so
     * an album does not put every serialised document into one response.
     *
     * @return void
     */
    public function test_page_inlines_only_the_first_map(): void {
        global $PAGE;
        $this->resetAfterTest();
        [$course, $cm, , $maps] = $this->make();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);

        $cminfo = get_fast_modinfo($course)->get_cm($cm->id);
        $PAGE->set_url('/mod/vimigallery/view.php', ['id' => $cm->id]);
        $PAGE->set_context(\context_module::instance($cm->id));
        $renderer = $PAGE->get_renderer('mod_vimigallery');
        $html = $renderer->render(new \mod_vimigallery\output\gallery($cminfo));

        $this->assertStringContainsString(s($maps[0]), $html);
        $this->assertStringNotContainsString(s($maps[1]), $html);
        $this->assertStringNotContainsString(s($maps[2]), $html);
        // The remaining slides carry the id the viewer fetches them with.
        $this->assertStringContainsString('data-itemid', $html);
    }
}
