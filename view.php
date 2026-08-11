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
 * Display a ViMi Pad gallery.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

$id = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('vimigallery', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$gallery = $DB->get_record('vimigallery', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/vimigallery:view', $context);

$event = \core\event\course_module_viewed::create([
    'objectid' => $gallery->id,
    'context' => $context,
]);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('vimigallery', $gallery);
$event->trigger();

$PAGE->set_url('/mod/vimigallery/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($gallery->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->set_activity_record($gallery);

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($gallery->name));

if (has_capability('mod/vimigallery:manageitems', $context)) {
    echo html_writer::div(
        html_writer::link(
            new moodle_url('/mod/vimigallery/arrange.php', ['id' => $cm->id]),
            get_string('arrange', 'mod_vimigallery'),
            ['class' => 'btn btn-secondary btn-sm']
        ),
        'mb-3'
    );
}

if (!empty($gallery->intro)) {
    echo $OUTPUT->box(
        format_module_intro('vimigallery', $gallery, $cm->id),
        'generalbox mod_introbox',
        'vimigalleryintro'
    );
}

$cminfo = cm_info::create($cm);
$renderable = new \mod_vimigallery\output\gallery($cminfo);
echo $PAGE->get_renderer('mod_vimigallery')->render($renderable);

echo $OUTPUT->footer();
