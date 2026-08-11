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
 * Arrange (curate) the maps of a ViMi Pad gallery.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

use mod_vimigallery\local\curation;

$id = required_param('id', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$itemid = optional_param('item', 0, PARAM_INT);

$cm = get_coursemodule_from_id('vimigallery', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$gallery = $DB->get_record('vimigallery', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, false, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/vimigallery:manageitems', $context);

$pageurl = new moodle_url('/mod/vimigallery/arrange.php', ['id' => $cm->id]);

if ($action && confirm_sesskey()) {
    switch ($action) {
        case 'up':
            curation::move($gallery->id, $itemid, curation::UP);
            break;
        case 'down':
            curation::move($gallery->id, $itemid, curation::DOWN);
            break;
        case 'hide':
            curation::set_visible($gallery->id, $itemid, false);
            break;
        case 'show':
            curation::set_visible($gallery->id, $itemid, true);
            break;
        case 'refresh':
            vimigallery_rebuild_items($gallery->id, $context);
            break;
    }
    redirect($pageurl);
}

$PAGE->set_url($pageurl);
$PAGE->set_title(format_string($gallery->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('arrange', 'mod_vimigallery'));

echo html_writer::div(
    html_writer::link(
        new moodle_url('/mod/vimigallery/view.php', ['id' => $cm->id]),
        get_string('backtogallery', 'mod_vimigallery')
    ),
    'mb-3'
);

// A materialised source can be refreshed from its origin.
if ($gallery->sourcetype !== 'upload' && $gallery->freshness !== 'live') {
    echo html_writer::div(
        html_writer::link(
            new moodle_url($pageurl, ['action' => 'refresh', 'sesskey' => sesskey()]),
            get_string('refreshsnapshot', 'mod_vimigallery'),
            ['class' => 'btn btn-secondary']
        ),
        'mb-3'
    );
}

$items = array_values($DB->get_records('vimigallery_item', ['galleryid' => $gallery->id], 'sortorder ASC, id ASC'));

if (empty($items)) {
    echo html_writer::div(get_string('nomaps', 'mod_vimigallery'), 'text-muted');
    echo $OUTPUT->footer();
    exit;
}

$PAGE->requires->js_call_amd('mod_vimigallery/arrange', 'init', [$cm->id]);

echo html_writer::tag('p', get_string('arrange_help', 'mod_vimigallery'), ['class' => 'text-muted small']);

$last = count($items) - 1;
$rows = '';
foreach ($items as $pos => $item) {
    $handle = html_writer::tag(
        'span',
        $OUTPUT->pix_icon('i/dragdrop', get_string('move')),
        ['class' => 'vimigallery-drag-handle', 'title' => get_string('move')]
    );

    $up = ($pos > 0)
        ? html_writer::link(
            new moodle_url($pageurl, ['action' => 'up', 'item' => $item->id, 'sesskey' => sesskey()]),
            $OUTPUT->pix_icon('t/up', get_string('moveup'))
        )
        : '';
    $down = ($pos < $last)
        ? html_writer::link(
            new moodle_url($pageurl, ['action' => 'down', 'item' => $item->id, 'sesskey' => sesskey()]),
            $OUTPUT->pix_icon('t/down', get_string('movedown'))
        )
        : '';

    if ($item->visible) {
        $vis = html_writer::link(
            new moodle_url($pageurl, ['action' => 'hide', 'item' => $item->id, 'sesskey' => sesskey()]),
            $OUTPUT->pix_icon('t/hide', get_string('hide'))
        );
    } else {
        $vis = html_writer::link(
            new moodle_url($pageurl, ['action' => 'show', 'item' => $item->id, 'sesskey' => sesskey()]),
            $OUTPUT->pix_icon('t/show', get_string('show'))
        );
    }

    $label = html_writer::tag(
        'span',
        s($item->authorname !== '' ? $item->authorname : $item->profile),
        ['class' => 'vimigallery-arrange-label']
    );
    $meta = html_writer::tag('span', s($item->profile), ['class' => 'text-muted small ml-2']);
    $controls = html_writer::tag('span', $vis . ' ' . $up . ' ' . $down, ['class' => 'vimigallery-arrange-controls float-right']);

    $rowclass = 'vimigallery-arrange-item list-group-item' . ($item->visible ? '' : ' dimmed_text');
    $rows .= html_writer::tag(
        'li',
        $handle . ' ' . $label . $meta . $controls,
        ['class' => $rowclass, 'data-itemid' => $item->id]
    );
}

echo html_writer::tag('ul', $rows, ['class' => 'vimigallery-arrange-list list-group']);
echo $OUTPUT->footer();
