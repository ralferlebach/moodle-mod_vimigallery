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

use backup;
use backup_controller;
use backup_setting;
use restore_controller;
use restore_dbops;

/**
 * Backup and restore roundtrip for mod_vimigallery.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \backup_vimigallery_activity_structure_step
 * @covers     \restore_vimigallery_activity_structure_step
 */
final class backup_restore_test extends \advanced_testcase {
    /**
     * A gallery's settings, items and comments survive a course backup/restore.
     *
     * @return void
     */
    public function test_roundtrip_with_comments(): void {
        global $DB, $USER, $CFG;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

        $this->resetAfterTest();
        $this->setAdminUser();
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        // Source course with a fully configured gallery.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $module = $this->getDataGenerator()->create_module('vimigallery', [
            'course' => $course->id,
            'allowcomments' => 1,
            'enablecompare' => 1,
            'completioncommentsmin' => 2,
        ]);
        $cm = get_coursemodule_from_instance('vimigallery', $module->id);
        $mapjson = '{"profile":"conceptmap","nodes":[],"relations":[]}';
        $itemid = (int) $DB->insert_record('vimigallery_item', (object) [
            'galleryid' => $module->id,
            'sortorder' => 0,
            'visible' => 1,
            'sourcetype' => 'upload',
            'profile' => 'conceptmap',
            'mapjson' => $mapjson,
            'authorname' => 'Alice',
            'contenthash' => sha1($mapjson),
            'timecreated' => time(),
        ]);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        local\comment_service::post($cm, $itemid, (int) $student->id, 'Great map');

        // Backup the course with user data.
        $bc = new backup_controller(
            backup::TYPE_1COURSE,
            $course->id,
            backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO,
            backup::MODE_IMPORT,
            $USER->id
        );
        $bc->get_plan()->get_setting('users')->set_status(backup_setting::NOT_LOCKED);
        $bc->get_plan()->get_setting('users')->set_value(true);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Restore into a fresh course.
        $newcourseid = restore_dbops::create_new_course(
            'Restored',
            'restored_' . uniqid(),
            $course->category
        );
        $rc = new restore_controller(
            $backupid,
            $newcourseid,
            backup::INTERACTIVE_NO,
            backup::MODE_GENERAL,
            $USER->id,
            backup::TARGET_NEW_COURSE
        );
        $rc->get_plan()->get_setting('users')->set_status(backup_setting::NOT_LOCKED);
        $rc->get_plan()->get_setting('users')->set_value(true);
        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        // The restored gallery keeps its settings.
        $restored = $DB->get_record('vimigallery', ['course' => $newcourseid], '*', MUST_EXIST);
        $this->assertEquals(1, $restored->allowcomments);
        $this->assertEquals(1, $restored->enablecompare);
        $this->assertEquals(2, $restored->completioncommentsmin);

        // The item is restored with its content hash.
        $items = $DB->get_records('vimigallery_item', ['galleryid' => $restored->id]);
        $this->assertCount(1, $items);
        $restoreditem = reset($items);
        $this->assertSame(sha1($mapjson), $restoreditem->contenthash);
        $this->assertSame('Alice', $restoreditem->authorname);

        // The comment is restored, linked to the new item and the same user.
        $comments = $DB->get_records('vimigallery_comment', ['galleryid' => $restored->id]);
        $this->assertCount(1, $comments);
        $restoredcomment = reset($comments);
        $this->assertSame('Great map', $restoredcomment->content);
        $this->assertEquals((int) $restoreditem->id, (int) $restoredcomment->itemid);
        $this->assertEquals((int) $student->id, (int) $restoredcomment->userid);
    }
}
