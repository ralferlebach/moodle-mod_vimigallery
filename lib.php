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
 * Library of interface functions for mod_vimigallery.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Declare which optional module features are supported.
 *
 * @param string $feature One of the FEATURE_* constants.
 * @return mixed True/false for the feature, or null if unknown.
 */
function vimigallery_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
        case FEATURE_SHOW_DESCRIPTION:
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return false;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_CONTENT;
        default:
            return null;
    }
}

/**
 * Build the source adapter for a gallery, or null for uploaded maps.
 *
 * @param stdClass $gallery The gallery instance record.
 * @return \mod_vimigallery\source\source_interface|null The adapter, or null.
 */
function vimigallery_make_source($gallery) {
    switch ($gallery->sourcetype) {
        case 'datafield':
            return new \mod_vimigallery\source\datafield_source(
                (int) $gallery->sourcecmid,
                (int) $gallery->sourcefieldid
            );
        case 'qtype':
            return new \mod_vimigallery\source\qtype_source(
                (int) $gallery->sourcecmid,
                $gallery->sourcemode
            );
        default:
            return null;
    }
}

/**
 * Resolve the chosen source selection into the stored source columns.
 *
 * @param stdClass $data The form data (modified in place).
 * @return void
 */
function vimigallery_prepare_source_fields($data) {
    if (empty($data->sourcetype)) {
        $data->sourcetype = 'upload';
    }
    if (empty($data->freshness)) {
        $data->freshness = 'live';
    }
    if (empty($data->sourcemode)) {
        $data->sourcemode = 'reference';
    }
    $data->sourcecmid = 0;
    $data->sourcefieldid = 0;
    if ($data->sourcetype === 'datafield' && !empty($data->datafieldsource)) {
        $parts = explode(':', $data->datafieldsource);
        if (count($parts) === 2) {
            $data->sourcecmid = (int) $parts[0];
            $data->sourcefieldid = (int) $parts[1];
        }
    } else if ($data->sourcetype === 'qtype' && !empty($data->qtypesource)) {
        $data->sourcecmid = (int) $data->qtypesource;
    }
}

/**
 * Add a new gallery instance.
 *
 * @param stdClass $data The form data.
 * @param mod_vimigallery_mod_form|null $mform The form.
 * @return int The new instance id.
 */
function vimigallery_add_instance($data, $mform = null) {
    global $DB;

    vimigallery_prepare_source_fields($data);
    $data->timemodified = time();
    $data->id = $DB->insert_record('vimigallery', $data);

    $context = context_module::instance($data->coursemodule);
    vimigallery_save_source_files($data, $context);
    vimigallery_rebuild_items($data->id, $context);

    return $data->id;
}

/**
 * Update an existing gallery instance.
 *
 * @param stdClass $data The form data.
 * @param mod_vimigallery_mod_form|null $mform The form.
 * @return bool Always true.
 */
function vimigallery_update_instance($data, $mform = null) {
    global $DB;

    vimigallery_prepare_source_fields($data);
    $data->id = $data->instance;
    $data->timemodified = time();
    $DB->update_record('vimigallery', $data);

    $context = context_module::instance($data->coursemodule);
    vimigallery_save_source_files($data, $context);
    vimigallery_rebuild_items($data->id, $context);

    return true;
}

/**
 * Delete a gallery instance and its items.
 *
 * @param int $id The instance id.
 * @return bool Always true.
 */
function vimigallery_delete_instance($id) {
    global $DB;

    if (!$DB->record_exists('vimigallery', ['id' => $id])) {
        return true;
    }
    $DB->delete_records('vimigallery_item', ['galleryid' => $id]);
    $DB->delete_records('vimigallery', ['id' => $id]);

    return true;
}

/**
 * Persist the uploaded source files (JSON maps) into the module file area.
 *
 * @param stdClass $data The form data (carries the draft item id in vimijson).
 * @param context_module $context The module context.
 * @return void
 */
function vimigallery_save_source_files($data, context_module $context) {
    if (empty($data->vimijson)) {
        return;
    }
    file_save_draft_area_files(
        $data->vimijson,
        $context->id,
        'mod_vimigallery',
        'source',
        0,
        ['subdirs' => 0, 'maxfiles' => 200, 'accepted_types' => ['.json']]
    );
}

/**
 * Rebuild the gallery items from the stored source files.
 *
 * Each JSON file becomes one frozen item. Files that do not parse into a map are
 * skipped. Ordering follows the file name for a stable, predictable sequence.
 *
 * @param int $galleryid The gallery instance id.
 * @param context_module $context The module context.
 * @return void
 */
function vimigallery_rebuild_items($galleryid, context_module $context) {
    global $DB;

    $gallery = $DB->get_record('vimigallery', ['id' => $galleryid], '*', MUST_EXIST);
    $DB->delete_records('vimigallery_item', ['galleryid' => $galleryid]);

    // Activity sources: materialise for static/snapshot; live keeps no items.
    $source = vimigallery_make_source($gallery);
    if ($source !== null) {
        if ($gallery->freshness === 'live') {
            return;
        }
        $sortorder = 0;
        foreach ($source->get_items() as $sourceitem) {
            $DB->insert_record('vimigallery_item', (object) [
                'galleryid' => $galleryid,
                'sortorder' => $sortorder++,
                'visible' => 1,
                'sourcetype' => $gallery->sourcetype,
                'profile' => $sourceitem->profile,
                'mapjson' => $sourceitem->mapjson,
                'authorname' => $sourceitem->authorname,
                'timecreated' => time(),
            ]);
        }
        return;
    }

    // Upload source (default): one frozen item per stored JSON file.
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_vimigallery', 'source', 0, 'filename', false);

    $sortorder = 0;
    foreach ($files as $file) {
        $decoded = json_decode($file->get_content(), true);
        if (!is_array($decoded) || !isset($decoded['nodes'])) {
            continue;
        }
        $profile = isset($decoded['profile']) && is_string($decoded['profile'])
            ? $decoded['profile'] : 'conceptmap';
        $author = isset($decoded['author']) && is_string($decoded['author'])
            ? $decoded['author'] : '';
        $DB->insert_record('vimigallery_item', (object) [
            'galleryid' => $galleryid,
            'sortorder' => $sortorder++,
            'visible' => 1,
            'sourcetype' => 'upload',
            'profile' => core_text::substr($profile, 0, 40),
            'mapjson' => $file->get_content(),
            'authorname' => core_text::substr($author, 0, 255),
            'timecreated' => time(),
        ]);
    }
}

/**
 * List the ViMi Pad database fields available as gallery sources in a course.
 *
 * @param int $courseid The course id.
 * @return array Map of "cmid:fieldid" => "Database name: Field name".
 */
function vimigallery_list_datafield_sources($courseid) {
    global $DB;

    $options = [];
    $modinfo = get_fast_modinfo($courseid);
    foreach ($modinfo->get_instances_of('data') as $cm) {
        $fields = $DB->get_records('data_fields', ['dataid' => $cm->instance, 'type' => 'vimipad'], 'name ASC');
        foreach ($fields as $field) {
            $key = $cm->id . ':' . $field->id;
            $options[$key] = format_string($cm->name) . ': ' . format_string($field->name);
        }
    }
    return $options;
}

/**
 * List the Quiz activities available as gallery sources in a course.
 *
 * @param int $courseid The course id.
 * @return array Map of cmid => quiz name.
 */
function vimigallery_list_quiz_sources($courseid) {
    $options = [];
    $modinfo = get_fast_modinfo($courseid);
    foreach ($modinfo->get_instances_of('quiz') as $cm) {
        $options[$cm->id] = format_string($cm->name);
    }
    return $options;
}

/**
 * Course-module info: name, description content and the display mode.
 *
 * @param stdClass $coursemodule The course module record.
 * @return cached_cm_info|null The cached info, or null when missing.
 */
function vimigallery_get_coursemodule_info($coursemodule) {
    global $DB;

    $gallery = $DB->get_record(
        'vimigallery',
        ['id' => $coursemodule->instance],
        'id, name, intro, introformat, displaymode',
        MUST_EXIST
    );

    $info = new cached_cm_info();
    $info->name = $gallery->name;
    // Carry the display mode so cm_info_view can decide without another query.
    $info->customdata = ['displaymode' => $gallery->displaymode];

    if ($coursemodule->showdescription) {
        $info->content = format_module_intro('vimigallery', $gallery, $coursemodule->id, false);
    }

    return $info;
}

/**
 * Dynamic course-page rendering. In course display mode the gallery is embedded
 * directly (like a label) with no link; in page mode the normal link is kept.
 *
 * @param cm_info $cm The course module.
 * @return void
 */
function vimigallery_cm_info_view(cm_info $cm) {
    $customdata = $cm->customdata;
    $displaymode = is_array($customdata) && isset($customdata['displaymode'])
        ? $customdata['displaymode'] : 'page';

    if ($displaymode !== 'course') {
        return;
    }

    global $PAGE;
    $output = new \mod_vimigallery\output\gallery($cm);
    $html = $PAGE->get_renderer('mod_vimigallery')->render($output);
    $cm->set_content($html, true);
    $cm->set_no_view_link();
}
