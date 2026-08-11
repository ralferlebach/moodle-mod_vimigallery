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
     * Create a submitted individual workspace with the given map.
     *
     * @param int $vimipadid The activity instance id.
     * @param int $userid The submitting user.
     * @param int $groupid The group id, or 0 for an individual workspace.
     * @return void
     */
    private function submit(int $vimipadid, int $userid, int $groupid = 0): void {
        global $DB;
        $wsid = $DB->insert_record('vimipad_workspace', (object) [
            'vimipadid' => $vimipadid,
            'userid' => $groupid ? null : $userid,
            'groupid' => $groupid ?: null,
            'name' => '',
            'currentrevision' => 1,
            'submittedsnapshotid' => null,
            'locked' => 0,
            'timecreated' => time(),
            'timemodified' => time(),
        ]);
        $sid = $DB->insert_record('vimipad_snapshot', (object) [
            'workspaceid' => $wsid,
            'revision' => 1,
            'snapshotjson' => $this->map(),
            'submittedby' => $userid,
            'status' => 1,
            'cohortjson' => '',
            'timecreated' => time(),
        ]);
        $DB->set_field('vimipad_workspace', 'submittedsnapshotid', $sid, ['id' => $wsid]);
    }

    /**
     * Have a user complete a quiz attempt answering the first slot with a map.
     *
     * @param \stdClass $quiz The quiz instance.
     * @param int $userid The attempting user.
     * @param string $map The map JSON answer.
     * @return void
     */
    private function submit_attempt(\stdClass $quiz, int $userid, string $map): void {
        $quizobj = \mod_quiz\quiz_settings::create($quiz->id, $userid);
        $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $userid);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);
        $attemptobj = \mod_quiz\quiz_attempt::create($attempt->id);
        $attemptobj->process_submitted_actions($timenow, false, [1 => ['answer' => $map]]);
        $attemptobj->process_finish($timenow, false);
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

    /**
     * Reference mode exposes model solutions only to users who may grade.
     *
     * @return void
     */
    public function test_qtype_reference_visibility(): void {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/quiz/locallib.php');
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();

        $course = $gen->create_course();
        $quiz = $gen->create_module('quiz', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('quiz', $quiz->id);

        /** @var \core_question_generator $qgen */
        $qgen = $gen->get_plugin_generator('core_question');
        $cat = $qgen->create_question_category();
        $question = $qgen->create_question('vimipad', 'stub', ['category' => $cat->id]);

        // Give the question a model solution and place it in the quiz.
        $DB->set_field('qtype_vimipad_options', 'referencemap', $this->map(), ['questionid' => $question->id]);
        quiz_add_quiz_question($question->id, $quiz);

        $teacher = $gen->create_and_enrol($course, 'editingteacher');
        $student = $gen->create_and_enrol($course, 'student');

        $source = new \mod_vimigallery\source\qtype_source($cm->id, 'reference');

        // A grader sees the model solution.
        $this->assertCount(1, $source->get_items($teacher->id));
        // A student does not.
        $this->assertCount(0, $source->get_items($student->id));
    }
    /**
     * Submissions mode: learners see only their own; graders see all.
     *
     * @covers \mod_vimigallery\source\vimipad_source
     * @return void
     */
    public function test_vimipad_submissions_visibility(): void {
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();

        $course = $gen->create_course();
        $vimipad = $gen->create_module('vimipad', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vimipad', $vimipad->id);

        $student1 = $gen->create_and_enrol($course, 'student');
        $student2 = $gen->create_and_enrol($course, 'student');
        $teacher = $gen->create_and_enrol($course, 'editingteacher');

        $this->submit($vimipad->id, $student1->id);
        $this->submit($vimipad->id, $student2->id);

        $source = new \mod_vimigallery\source\vimipad_source($cm->id, 'submissions');

        $this->assertCount(2, $source->get_items($teacher->id));
        $this->assertCount(1, $source->get_items($student1->id));
        $this->assertCount(1, $source->get_items($student2->id));
    }

    /**
     * Reference mode: model solution is shown to graders only.
     *
     * @covers \mod_vimigallery\source\vimipad_source
     * @return void
     */
    public function test_vimipad_reference_visibility(): void {
        global $DB;
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();

        $course = $gen->create_course();
        $vimipad = $gen->create_module('vimipad', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vimipad', $vimipad->id);
        $DB->set_field('vimipad', 'referencemapjson', $this->map(), ['id' => $vimipad->id]);

        $student = $gen->create_and_enrol($course, 'student');
        $teacher = $gen->create_and_enrol($course, 'editingteacher');

        $source = new \mod_vimigallery\source\vimipad_source($cm->id, 'reference');

        $this->assertCount(1, $source->get_items($teacher->id));
        $this->assertCount(0, $source->get_items($student->id));
    }
    /**
     * Qtype submissions: learners see only their own; graders see all.
     *
     * @covers \mod_vimigallery\source\qtype_source
     * @return void
     */
    public function test_qtype_submissions_visibility(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/quiz/locallib.php');
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();

        $course = $gen->create_course();
        $quizgen = $gen->get_plugin_generator('mod_quiz');
        $quiz = $quizgen->create_instance(['course' => $course->id, 'grade' => 100.0, 'sumgrades' => 1]);
        $cm = get_coursemodule_from_instance('quiz', $quiz->id);

        $qgen = $gen->get_plugin_generator('core_question');
        $cat = $qgen->create_question_category();
        $question = $qgen->create_question('vimipad', 'stub', ['category' => $cat->id]);
        quiz_add_quiz_question($question->id, $quiz);

        $student1 = $gen->create_and_enrol($course, 'student');
        $student2 = $gen->create_and_enrol($course, 'student');
        $teacher = $gen->create_and_enrol($course, 'editingteacher');

        $this->submit_attempt($quiz, (int) $student1->id, $this->map());

        $source = new \mod_vimigallery\source\qtype_source($cm->id, 'submissions');

        $this->assertCount(1, $source->get_items($teacher->id));
        $this->assertCount(1, $source->get_items($student1->id));
        $this->assertCount(0, $source->get_items($student2->id));
    }
}
