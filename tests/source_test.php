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

use mod_vimigallery\source\datafield_source;

/**
 * Tests for the datafield gallery source and its visibility rules.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_vimigallery\source\datafield_source
 */
final class source_test extends \advanced_testcase {
    /** @var string A minimal valid map value. */
    private string $map;

    /**
     * A valid map JSON for entries.
     *
     * @return string
     */
    private function map(): string {
        return json_encode([
            'profile' => 'conceptmap',
            'nodes' => [['stableid' => 'a', 'label' => 'X']],
            'relations' => [],
        ]);
    }

    /**
     * Unapproved entries are hidden from ordinary viewers but visible to owners
     * and to users who may approve.
     *
     * @return void
     */
    public function test_approval_visibility(): void {
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();

        $course = $gen->create_course();
        $data = $gen->create_module('data', ['course' => $course->id, 'approval' => 1]);
        $cm = get_coursemodule_from_instance('data', $data->id);

        /** @var \mod_data_generator $dgen */
        $dgen = $gen->get_plugin_generator('mod_data');
        $field = $dgen->create_field(
            (object) ['type' => 'vimipad', 'name' => 'Map', 'dataid' => $data->id],
            $data
        );
        $fieldid = $field->field->id;

        $student1 = $gen->create_and_enrol($course, 'student');
        $student2 = $gen->create_and_enrol($course, 'student');
        $teacher = $gen->create_and_enrol($course, 'editingteacher');

        $dgen->create_entry($data, [$fieldid => $this->map()], 0, [], ['approved' => true], $student1->id);
        $dgen->create_entry($data, [$fieldid => $this->map()], 0, [], ['approved' => false], $student1->id);

        $source = new datafield_source($cm->id, $fieldid);

        // Another student sees only the approved entry.
        $this->assertCount(1, $source->get_items($student2->id));
        // The owner sees their own unapproved entry too.
        $this->assertCount(2, $source->get_items($student1->id));
        // A teacher who may approve sees both.
        $this->assertCount(2, $source->get_items($teacher->id));
    }

    /**
     * Separate groups hide entries from users who are not in the group.
     *
     * @return void
     */
    public function test_separate_groups_visibility(): void {
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();

        $course = $gen->create_course();
        // SEPARATEGROUPS = 1, forced.
        $data = $gen->create_module('data', [
            'course' => $course->id,
            'groupmode' => SEPARATEGROUPS,
            'groupmodeforce' => 1,
        ]);
        $cm = get_coursemodule_from_instance('data', $data->id);

        /** @var \mod_data_generator $dgen */
        $dgen = $gen->get_plugin_generator('mod_data');
        $field = $dgen->create_field(
            (object) ['type' => 'vimipad', 'name' => 'Map', 'dataid' => $data->id],
            $data
        );
        $fieldid = $field->field->id;

        $group1 = $gen->create_group(['courseid' => $course->id]);
        $group2 = $gen->create_group(['courseid' => $course->id]);

        $ingroup1 = $gen->create_and_enrol($course, 'student');
        $ingroup2 = $gen->create_and_enrol($course, 'student');
        $gen->create_group_member(['groupid' => $group1->id, 'userid' => $ingroup1->id]);
        $gen->create_group_member(['groupid' => $group2->id, 'userid' => $ingroup2->id]);

        $dgen->create_entry($data, [$fieldid => $this->map()], $group1->id, [], null, $ingroup1->id);

        $source = new datafield_source($cm->id, $fieldid);

        // The group-1 member sees the group-1 entry.
        $this->assertCount(1, $source->get_items($ingroup1->id));
        // The group-2 member does not.
        $this->assertCount(0, $source->get_items($ingroup2->id));
    }

    /**
     * A wrong or non-ViMi field id yields nothing rather than leaking content.
     *
     * @return void
     */
    public function test_invalid_field_returns_empty(): void {
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();

        $course = $gen->create_course();
        $data = $gen->create_module('data', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('data', $data->id);
        $teacher = $gen->create_and_enrol($course, 'editingteacher');

        // Field id 0 does not exist.
        $source = new datafield_source($cm->id, 0);
        $this->assertSame([], $source->get_items($teacher->id));
    }
}
