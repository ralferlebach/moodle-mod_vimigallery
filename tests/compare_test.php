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

use mod_vimigallery\output\compare;

/**
 * Tests for the side-by-side comparison renderable.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_vimigallery\output\compare
 */
final class compare_test extends \advanced_testcase {
    /**
     * A small snapshot-shaped map with two linked concepts.
     *
     * @param string $second The label of the second concept.
     * @return string The map JSON.
     */
    private function map(string $second): string {
        return json_encode([
            'profile' => 'conceptmap',
            'nodes' => [
                ['stableid' => 'n1', 'label' => 'Cat'],
                ['stableid' => 'n2', 'label' => $second],
            ],
            'relations' => [
                ['sourceid' => 'n1', 'targetid' => 'n2', 'label' => 'is a'],
            ],
        ]);
    }

    /**
     * Create a gallery with two items and return [cminfo, id1, id2].
     *
     * @param string $secondlabel Label for the second map's second concept.
     * @return array [cm_info, string id1, string id2]
     */
    private function make(string $secondlabel = 'Animal'): array {
        global $DB;
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module(
            'vimigallery',
            ['course' => $course->id, 'enablecompare' => 1]
        );
        $mk = function (string $json) use ($DB, $module) {
            return (string) $DB->insert_record('vimigallery_item', (object) [
                'galleryid' => $module->id,
                'sortorder' => 0,
                'visible' => 1,
                'sourcetype' => 'upload',
                'profile' => 'conceptmap',
                'mapjson' => $json,
                'authorname' => '',
                'contenthash' => sha1($json),
                'timecreated' => time(),
            ]);
        };
        $id1 = $mk($this->map('Animal'));
        $id2 = $mk($this->map($secondlabel));
        $cm = get_coursemodule_from_instance('vimigallery', $module->id);
        $cminfo = get_fast_modinfo($course)->get_cm($cm->id);
        return [$cminfo, $id1, $id2];
    }

    /**
     * The renderable resolves the chosen items by id.
     *
     * @return void
     */
    public function test_resolves_items(): void {
        $this->resetAfterTest();
        [$cminfo, $id1, $id2] = $this->make();

        $compare = new compare($cminfo, $id1, $id2);
        $this->assertNotNull($compare->get_left());
        $this->assertNotNull($compare->get_right());
        $this->assertSame($id1, (string) $compare->get_left()->id);
        $this->assertSame($id2, (string) $compare->get_right()->id);
        $this->assertCount(2, $compare->get_items());
    }

    /**
     * Two identical maps score as fully similar.
     *
     * @return void
     */
    public function test_similarity_identical(): void {
        $this->resetAfterTest();
        [$cminfo, $id1, $id2] = $this->make('Animal');

        $compare = new compare($cminfo, $id1, $id2);
        $similarity = $compare->similarity();
        $this->assertNotNull($similarity);
        $this->assertEqualsWithDelta(1.0, $similarity, 0.001);
    }

    /**
     * A different second map scores below full similarity.
     *
     * @return void
     */
    public function test_similarity_differs(): void {
        $this->resetAfterTest();
        [$cminfo, $id1, $id2] = $this->make('Machine');

        $compare = new compare($cminfo, $id1, $id2);
        $similarity = $compare->similarity();
        $this->assertNotNull($similarity);
        $this->assertLessThan(1.0, $similarity);
    }

    /**
     * With only one side chosen, no similarity is produced.
     *
     * @return void
     */
    public function test_similarity_needs_both(): void {
        $this->resetAfterTest();
        [$cminfo, $id1] = $this->make();

        $compare = new compare($cminfo, $id1, '');
        $this->assertNull($compare->get_right());
        $this->assertNull($compare->similarity());
    }
}
