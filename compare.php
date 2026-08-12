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
 * Side-by-side map comparison for a ViMi Pad gallery.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

$id = required_param('id', PARAM_INT);
$leftid = optional_param('left', '', PARAM_ALPHANUMEXT);
$rightid = optional_param('right', '', PARAM_ALPHANUMEXT);

$cm = get_coursemodule_from_id('vimigallery', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
require_login($course, true, $cm);

$context = context_module::instance($cm->id);
$modinfo = get_fast_modinfo($course);
$cminfo = $modinfo->get_cm($cm->id);

$gallery = new \mod_vimigallery\output\gallery($cminfo);
if (!$gallery->enable_compare()) {
    redirect(new moodle_url('/mod/vimigallery/view.php', ['id' => $id]));
}

$PAGE->set_url('/mod/vimigallery/compare.php', ['id' => $id]);
$PAGE->set_context($context);
$PAGE->set_cm($cm, $course);
$PAGE->set_title(format_string($cm->name));
$PAGE->set_heading(format_string($course->fullname));

$compare = new \mod_vimigallery\output\compare($cminfo, $leftid, $rightid);
$output = $PAGE->get_renderer('mod_vimigallery');

echo $output->header();
echo $output->heading(get_string('compare', 'mod_vimigallery'));
echo $output->render($compare);
echo $output->footer();
