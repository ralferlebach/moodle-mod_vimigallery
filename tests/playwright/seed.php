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

/**
 * CLI seed for the mod_vimigallery Playwright user stories.
 *
 * Creates a course with a teacher and a student, a gallery with comments and
 * side-by-side comparison enabled, and two materialised upload maps, then prints
 * the environment the Playwright run reads. For a disposable dev/CI site only.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir . '/enrollib.php');

/**
 * Create or fetch a user with a known password, enrolled into a course.
 *
 * @param string $base Username stem.
 * @param string $first First name.
 * @param string $last Last name.
 * @param string $pass Password.
 * @param int $courseid Course to enrol into.
 * @param string $rolename Archetype role shortname.
 * @return stdClass The user record.
 */
function vimigallery_seed_user($base, $first, $last, $pass, $courseid, $rolename) {
    global $DB, $CFG;
    $username = $base . '_' . $courseid;
    $user = $DB->get_record('user', ['username' => $username]);
    if (!$user) {
        $user = (object) [
            'username' => $username,
            'auth' => 'manual',
            'confirmed' => 1,
            'firstname' => $first,
            'lastname' => $last,
            'email' => $username . '@example.invalid',
            'mnethostid' => $CFG->mnet_localhost_id,
        ];
        $user->id = user_create_user($user, false, false);
    }
    update_internal_user_password($DB->get_record('user', ['id' => $user->id]), $pass);
    $role = $DB->get_record('role', ['archetype' => $rolename], '*', MUST_EXIST);
    $manual = enrol_get_plugin('manual');
    $instance = $DB->get_record('enrol', ['courseid' => $courseid, 'enrol' => 'manual'], '*', MUST_EXIST);
    $manual->enrol_user($instance, $user->id, $role->id);
    return $user;
}

$now = time();
$course = create_course((object) [
    'fullname' => 'ViMi Gallery stories ' . $now,
    'shortname' => 'vgstories' . $now,
    'category' => 1,
    'numsections' => 1,
]);

$teacher = vimigallery_seed_user('vgal_t', 'Tay', 'Teacher', 'Vimi!gal_T1', $course->id, 'editingteacher');
$student = vimigallery_seed_user('vgal_s', 'Sam', 'Student', 'Vimi!gal_S1', $course->id, 'student');

$module = $DB->get_record('modules', ['name' => 'vimigallery'], '*', MUST_EXIST);
$created = add_moduleinfo((object) [
    'modulename' => 'vimigallery',
    'module' => $module->id,
    'course' => $course->id,
    'section' => 1,
    'visible' => 1,
    'name' => 'Class album',
    'intro' => '',
    'introformat' => FORMAT_HTML,
    'displaymode' => 'page',
    'showtabs' => 1,
    'showauthors' => 1,
    'sourcetype' => 'upload',
    'freshness' => 'live',
    'sourcemode' => 'reference',
    'allowcomments' => 1,
    'enablecompare' => 1,
    'completioncommentsmin' => 0,
], $course);
$cmid = (int) $created->coursemodule;
$galleryid = (int) $created->instance;

// Two materialised maps so the album, comments and compare have content.
foreach ([['Water cycle', 'a'], ['Food web', 'b']] as $i => $pair) {
    [$title, $key] = $pair;
    $map = json_encode([
        'profile' => 'conceptmap',
        'nodes' => [
            ['stableid' => 'n1', 'label' => $title],
            ['stableid' => 'n2', 'label' => $title . ' detail'],
        ],
        'relations' => [['stableid' => 'r1', 'sourceid' => 'n1', 'targetid' => 'n2', 'label' => 'includes']],
    ]);
    $DB->insert_record('vimigallery_item', (object) [
        'galleryid' => $galleryid,
        'sortorder' => $i,
        'visible' => 1,
        'sourcetype' => 'upload',
        'profile' => 'conceptmap',
        'mapjson' => $map,
        'authorname' => 'Author ' . strtoupper($key),
        'contenthash' => sha1($key),
        'sourceuserid' => null,
        'sourcekey' => 'upload:' . $key,
        'timecreated' => $now,
    ]);
}

$path = '/mod/vimigallery/view.php?id=' . $cmid;

echo "export VIMIGALLERY_BASE_URL='{$CFG->wwwroot}'\n";
echo "export VIMIGALLERY_PATH='{$path}'\n";
echo "export VIMIGALLERY_CMID='{$cmid}'\n";
echo "export VIMIGALLERY_TEACHER='{$teacher->username}'\n";
echo "export VIMIGALLERY_TEACHER_PASS='Vimi!gal_T1'\n";
echo "export VIMIGALLERY_TEACHER_NAME='Tay Teacher'\n";
echo "export VIMIGALLERY_STUDENT='{$student->username}'\n";
echo "export VIMIGALLERY_STUDENT_PASS='Vimi!gal_S1'\n";
echo "export VIMIGALLERY_STUDENT_NAME='Sam Student'\n";
