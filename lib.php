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
        case FEATURE_COMPLETION_HAS_RULES:
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
        case 'vimipad':
            return new \mod_vimigallery\source\vimipad_source(
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
    $data->sourcetype = empty($data->sourcetype) ? 'upload' : $data->sourcetype;
    $data->freshness = empty($data->freshness) ? 'live' : $data->freshness;
    $data->sourcemode = empty($data->sourcemode) ? 'reference' : $data->sourcemode;

    vimigallery_decode_source_selection($data);
    vimigallery_validate_source_selection($data);
}

/**
 * Turn the form's per-type source selector into sourcecmid/sourcefieldid.
 *
 * @param object $data The submitted module data.
 * @return void
 */
function vimigallery_decode_source_selection($data) {
    $data->sourcecmid = 0;
    $data->sourcefieldid = 0;

    $selectors = [
        'datafield' => 'datafieldsource',
        'qtype' => 'qtypesource',
        'vimipad' => 'vimipadsource',
    ];
    $field = $selectors[$data->sourcetype] ?? null;
    if ($field === null || empty($data->$field)) {
        return;
    }

    if ($data->sourcetype !== 'datafield') {
        $data->sourcecmid = (int) $data->$field;
        return;
    }

    // The database selector carries "cmid:fieldid".
    $parts = explode(':', $data->$field);
    if (count($parts) === 2) {
        $data->sourcecmid = (int) $parts[0];
        $data->sourcefieldid = (int) $parts[1];
    }
}

/**
 * Verify that a chosen source really is a source this gallery may use.
 *
 * The form only offers activities from the gallery's own course, but the posted
 * ids are just numbers and can be swapped for any other course module. Without
 * this check a teacher with access to two courses could materialise content from
 * course B into a gallery in course A, contrary to the same-course contract the
 * UI states. Anything that does not check out falls back to an upload gallery
 * rather than silently pointing somewhere unintended.
 *
 * @param object $data The submitted module data (already carrying sourcecmid).
 * @return void
 */
function vimigallery_validate_source_selection($data) {
    if ($data->sourcetype === 'upload' || empty($data->sourcecmid)) {
        return;
    }

    if (!vimigallery_source_selection_is_valid($data)) {
        $data->sourcetype = 'upload';
        $data->sourcecmid = 0;
        $data->sourcefieldid = 0;
    }
}

/**
 * Whether the chosen source module (and field) checks out for this gallery.
 *
 * @param object $data The submitted module data.
 * @return bool True when the selection may be used.
 */
function vimigallery_source_selection_is_valid($data) {
    global $DB;

    $expected = [
        'datafield' => 'data',
        'qtype' => 'quiz',
        'vimipad' => 'vimipad',
    ];
    if (!isset($expected[$data->sourcetype])) {
        return false;
    }

    $cm = get_coursemodule_from_id($expected[$data->sourcetype], (int) $data->sourcecmid, 0, false, IGNORE_MISSING);
    if (!$cm || (int) $cm->course !== (int) $data->course) {
        return false;
    }
    if ($data->sourcetype !== 'datafield') {
        return true;
    }

    // The field must belong to this database activity and be a ViMi Pad field.
    $field = $DB->get_record('data_fields', ['id' => (int) $data->sourcefieldid], 'id, dataid, type');
    return $field && (int) $field->dataid === (int) $cm->instance && $field->type === 'vimipad';
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
    // Children first, so a database that enforces the foreign keys does not
    // refuse the delete and no orphaned comments can survive the gallery.
    $DB->delete_records('vimigallery_comment', ['galleryid' => $id]);
    $itemids = $DB->get_fieldset_select('vimigallery_item', 'id', 'galleryid = ?', [$id]);
    if (!empty($itemids)) {
        [$insql, $inparams] = $DB->get_in_or_equal($itemids);
        $DB->delete_records_select('vimigallery_item_user', "itemid $insql", $inparams);
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

    // Remember which content each existing comment belonged to, so comments can
    // survive a rebuild when the same map is still present.
    $oldcomments = $DB->get_records_sql(
        "SELECT c.id, i.sourcekey, i.contenthash
           FROM {vimigallery_comment} c
           JOIN {vimigallery_item} i ON i.id = c.itemid
          WHERE c.galleryid = :galleryid",
        ['galleryid' => $galleryid]
    );

    $entries = vimigallery_collect_entries($gallery, $context);

    // Everything above only reads. The destructive swap happens in one
    // transaction, so a failure while replacing the items rolls back to the
    // previous, working gallery instead of leaving it emptied.
    $transaction = $DB->start_delegated_transaction();
    $olditemids = $DB->get_fieldset_select('vimigallery_item', 'id', 'galleryid = ?', [$galleryid]);
    if (!empty($olditemids)) {
        [$insql, $inparams] = $DB->get_in_or_equal($olditemids);
        $DB->delete_records_select('vimigallery_item_user', "itemid $insql", $inparams);
    }
    $DB->delete_records('vimigallery_item', ['galleryid' => $galleryid]);
    $sortorder = 0;
    foreach ($entries as $entry) {
        $itemid = $DB->insert_record('vimigallery_item', (object) [
            'galleryid' => $galleryid,
            'sortorder' => $sortorder++,
            'visible' => 1,
            'sourcetype' => $entry['sourcetype'],
            'profile' => $entry['profile'],
            'mapjson' => $entry['mapjson'],
            'authorname' => $entry['authorname'],
            'contenthash' => sha1($entry['mapjson']),
            'sourceuserid' => $entry['sourceuserid'] ?? null,
            'sourcekey' => $entry['sourcekey'] ?? null,
            'timecreated' => time(),
        ]);
        foreach ($entry['contributors'] ?? [] as $contributor) {
            $DB->insert_record('vimigallery_item_user', (object) [
                'itemid' => $itemid,
                'userid' => (int) $contributor,
            ]);
        }
    }
    vimigallery_relink_comments($galleryid, $oldcomments);
    $transaction->allow_commit();
}

/**
 * Collect the map entries a rebuild should materialise, without touching the
 * database. Activity sources are read through their adapter (live galleries
 * keep nothing); an upload gallery reads its stored JSON files, each validated
 * against the public ViMi Pad map policy.
 *
 * @param stdClass $gallery The gallery instance.
 * @param context_module $context The gallery context.
 * @return array The entries to insert.
 */
function vimigallery_collect_entries($gallery, context_module $context) {
    $entries = [];
    $source = vimigallery_make_source($gallery);
    if ($source !== null) {
        // Activity sources: materialise for static/snapshot; live keeps no items.
        if ($gallery->freshness !== 'live') {
            foreach ($source->get_items() as $sourceitem) {
                $entries[] = [
                    'sourcetype' => $gallery->sourcetype,
                    'profile' => $sourceitem->profile,
                    'mapjson' => $sourceitem->mapjson,
                    'authorname' => $sourceitem->authorname,
                    'sourceuserid' => $sourceitem->sourceuserid ?? null,
                    // The source item id is already a stable origin identity
                    // (attempt+slot, snapshot, record), so it doubles as the key
                    // that carries comments across a rebuild.
                    'sourcekey' => (string) $sourceitem->id,
                    'contributors' => $sourceitem->contributors ?? [],
                ];
            }
        }
    } else {
        $entries = vimigallery_upload_entries($context);
    }
    return $entries;
}

/**
 * Read the gallery's uploaded JSON files as map entries.
 *
 * Uploads are teacher-supplied but still arbitrary input, so each file is
 * bounded in size and validated against the public ViMi Pad map policy before it
 * is materialised and later handed to the editor or the scorer.
 *
 * @param context_module $context The gallery context.
 * @return array The entries to insert.
 */
function vimigallery_upload_entries(context_module $context) {
    $entries = [];
    // Upload source (default): one frozen item per stored JSON file.
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_vimigallery', 'source', 0, 'filename', false);
    foreach ($files as $file) {
        if ($file->get_filesize() > \mod_vimipad\api\value::MAX_BYTES) {
            continue;
        }
        $content = $file->get_content();
        // Uploaded files are teacher-supplied but still arbitrary input, so
        // they must satisfy the public ViMi Pad map policy before they are
        // materialised and later handed to the editor or the scorer.
        if (!\mod_vimipad\api\value::is_valid($content)) {
            continue;
        }
        $decoded = json_decode($content, true);
        $profile = isset($decoded['profile']) && is_string($decoded['profile'])
            ? $decoded['profile'] : 'conceptmap';
        $author = isset($decoded['author']) && is_string($decoded['author'])
            ? $decoded['author'] : '';
        $entries[] = [
            'sourcetype' => 'upload',
            'profile' => core_text::substr($profile, 0, 40),
            'mapjson' => $content,
            'authorname' => core_text::substr($author, 0, 255),
            'sourceuserid' => null,
            // An uploaded file keeps its identity through its stored name, so a
            // comment stays with the same file across a rebuild even when two
            // uploads happen to hold identical maps.
            'sourcekey' => 'upload:' . $file->get_filename(),
        ];
    }

    return $entries;
}

/**
 * Re-attach or drop comments after a rebuild, matching on item content hash.
 *
 * @param int $galleryid The gallery instance id.
 * @param array $oldcomments Records of id => (id, contenthash) captured before rebuild.
 * @return void
 */
function vimigallery_relink_comments($galleryid, array $oldcomments) {
    global $DB;
    if (empty($oldcomments)) {
        return;
    }

    // Match on the origin identity, not on the content. Two maps can legitimately
    // be byte-identical - empty maps, a shared template, identical answers - and
    // a hash cannot tell them apart, so hash matching could move a comment onto
    // someone else's map. The content hash remains only as an integrity marker.
    $newitems = $DB->get_records('vimigallery_item', ['galleryid' => $galleryid], '', 'id, sourcekey');
    $keytoid = [];
    foreach ($newitems as $item) {
        if (!empty($item->sourcekey) && !isset($keytoid[$item->sourcekey])) {
            $keytoid[$item->sourcekey] = $item->id;
        }
    }

    foreach ($oldcomments as $comment) {
        $key = $comment->sourcekey ?? null;
        if (!empty($key) && isset($keytoid[$key])) {
            $DB->set_field('vimigallery_comment', 'itemid', $keytoid[$key], ['id' => $comment->id]);
        } else {
            // The map this comment belonged to is no longer in the gallery.
            $DB->delete_records('vimigallery_comment', ['id' => $comment->id]);
        }
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
 * List the ViMi Pad activities available as gallery sources in a course.
 *
 * @param int $courseid The course id.
 * @return array Map of cmid => activity name.
 */
function vimigallery_list_vimipad_sources($courseid) {
    $options = [];
    $modinfo = get_fast_modinfo($courseid);
    foreach ($modinfo->get_instances_of('vimipad') as $cm) {
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
        'id, name, intro, introformat, displaymode, completioncommentsmin',
        MUST_EXIST
    );

    $info = new cached_cm_info();
    $info->name = $gallery->name;
    // Carry the display mode so cm_info_view can decide without another query.
    $info->customdata = ['displaymode' => $gallery->displaymode];

    if ($coursemodule->completion == COMPLETION_TRACKING_AUTOMATIC) {
        $info->customdata['customcompletionrules']['completioncommentsmin'] =
            (int) $gallery->completioncommentsmin;
    }

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
