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

use mod_vimigallery\local\curation;

/**
 * Tests for the gallery curation service.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_vimigallery\local\curation
 */
final class curation_test extends \advanced_testcase {
    /**
     * Create a gallery with the given number of items.
     *
     * @param int $count The number of items.
     * @return array [galleryid, int[] itemids in order]
     */
    private function make_items(int $count): array {
        global $DB;
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('vimigallery', ['course' => $course->id]);
        $map = '{"profile":"conceptmap","nodes":[{"stableid":"a","label":"X"}],"relations":[]}';
        $ids = [];
        for ($i = 0; $i < $count; $i++) {
            $ids[] = (int) $DB->insert_record('vimigallery_item', (object) [
                'galleryid' => $module->id,
                'sortorder' => $i,
                'visible' => 1,
                'sourcetype' => 'upload',
                'profile' => 'conceptmap',
                'mapjson' => $map,
                'authorname' => 'A' . $i,
                'timecreated' => time(),
            ]);
        }
        return [(int) $module->id, $ids];
    }

    /**
     * The current item order (item ids by sortorder).
     *
     * @param int $galleryid The gallery id.
     * @return int[] The item ids in order.
     */
    private function order(int $galleryid): array {
        global $DB;
        $rows = $DB->get_records('vimigallery_item', ['galleryid' => $galleryid], 'sortorder ASC, id ASC');
        return array_map(fn($r) => (int) $r->id, array_values($rows));
    }

    /**
     * Moving up and down swaps neighbouring items.
     *
     * @return void
     */
    public function test_move(): void {
        $this->resetAfterTest();
        [$galleryid, $ids] = $this->make_items(3);

        // Move the last item one step towards the front.
        curation::move($galleryid, $ids[2], curation::UP);
        $this->assertSame([$ids[0], $ids[2], $ids[1]], $this->order($galleryid));

        // Then move the first item one step towards the back.
        curation::move($galleryid, $ids[0], curation::DOWN);
        $this->assertSame([$ids[2], $ids[0], $ids[1]], $this->order($galleryid));
    }

    /**
     * Moving beyond the boundaries is a no-op.
     *
     * @return void
     */
    public function test_move_boundaries(): void {
        $this->resetAfterTest();
        [$galleryid, $ids] = $this->make_items(3);

        curation::move($galleryid, $ids[0], curation::UP);
        $this->assertSame($ids, $this->order($galleryid));

        curation::move($galleryid, $ids[2], curation::DOWN);
        $this->assertSame($ids, $this->order($galleryid));
    }

    /**
     * Visibility can be toggled, and only for items of the gallery.
     *
     * @return void
     */
    public function test_set_visible(): void {
        global $DB;
        $this->resetAfterTest();
        [$galleryid, $ids] = $this->make_items(2);

        curation::set_visible($galleryid, $ids[0], false);
        $this->assertEquals(0, $DB->get_field('vimigallery_item', 'visible', ['id' => $ids[0]]));

        curation::set_visible($galleryid, $ids[0], true);
        $this->assertEquals(1, $DB->get_field('vimigallery_item', 'visible', ['id' => $ids[0]]));

        // A wrong gallery id must not change the item.
        curation::set_visible($galleryid + 999, $ids[1], false);
        $this->assertEquals(1, $DB->get_field('vimigallery_item', 'visible', ['id' => $ids[1]]));
    }
}
