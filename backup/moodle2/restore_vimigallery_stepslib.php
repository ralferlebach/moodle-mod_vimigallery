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
 * Restore structure step for mod_vimigallery.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the gallery structure for restore.
 */
class restore_vimigallery_activity_structure_step extends restore_activity_structure_step {
    /**
     * Define the restore path elements.
     *
     * @return array The restore paths, wrapped for the activity.
     */
    protected function define_structure() {
        $paths = [];
        $paths[] = new restore_path_element('vimigallery', '/activity/vimigallery');
        $paths[] = new restore_path_element('vimigallery_item', '/activity/vimigallery/items/item');
        if ($this->get_setting_value('userinfo')) {
            $paths[] = new restore_path_element(
                'vimigallery_comment',
                '/activity/vimigallery/items/item/comments/comment'
            );
        }
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Restore a gallery instance.
     *
     * @param array $data The instance data.
     * @return void
     */
    protected function process_vimigallery($data) {
        global $DB;
        $data = (object) $data;
        $data->course = $this->get_courseid();
        $data->timemodified = time();
        $newid = $DB->insert_record('vimigallery', $data);
        $this->apply_activity_instance($newid);
    }

    /**
     * Restore one gallery item.
     *
     * @param array $data The item data.
     * @return void
     */
    protected function process_vimigallery_item($data) {
        global $DB;
        $data = (object) $data;
        $oldid = $data->id;
        $data->galleryid = $this->get_new_parentid('vimigallery');
        if (!empty($data->sourceuserid)) {
            $data->sourceuserid = $this->get_mappingid('user', $data->sourceuserid) ?: null;
        }
        $newid = $DB->insert_record('vimigallery_item', $data);
        $this->set_mapping('vimigallery_item', $oldid, $newid);
    }

    /**
     * Restore one comment on a map.
     *
     * @param array $data The comment data.
     * @return void
     */
    protected function process_vimigallery_comment($data) {
        global $DB;
        $data = (object) $data;
        $data->itemid = $this->get_new_parentid('vimigallery_item');
        $data->galleryid = $this->get_new_parentid('vimigallery');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $DB->insert_record('vimigallery_comment', $data);
    }

    /**
     * Re-attach the module's files after restore.
     *
     * @return void
     */
    protected function after_execute() {
        $this->add_related_files('mod_vimigallery', 'intro', null);
        $this->add_related_files('mod_vimigallery', 'source', null);
    }

    /**
     * Remap the gallery's source activity once every module in the backup has
     * been restored.
     *
     * sourcecmid and sourcefieldid are ids of *another* activity, so they are
     * meaningless in the target site: left untouched they would point at a
     * missing module or, worse, at whatever unrelated module happens to carry
     * the same numeric id. The mappings only exist after all modules are in
     * place, which is why this runs in after_restore rather than while
     * processing the record. A source that did not travel with the backup is
     * cleared, turning the gallery into an empty upload gallery instead of a
     * gallery silently pointing somewhere wrong.
     *
     * @return void
     */
    protected function after_restore() {
        global $DB;

        $gallery = $DB->get_record('vimigallery', ['id' => $this->task->get_activityid()]);
        if (!$gallery || empty($gallery->sourcecmid) || $gallery->sourcetype === 'upload') {
            return;
        }

        $newcmid = $this->get_mappingid('course_module', $gallery->sourcecmid);
        if (!$newcmid) {
            $DB->update_record('vimigallery', (object) [
                'id' => $gallery->id,
                'sourcetype' => 'upload',
                'sourcecmid' => 0,
                'sourcefieldid' => 0,
            ]);
            return;
        }

        $update = (object) ['id' => $gallery->id, 'sourcecmid' => $newcmid];
        if ($gallery->sourcetype === 'datafield' && !empty($gallery->sourcefieldid)) {
            $newfieldid = $this->get_mappingid('data_field', $gallery->sourcefieldid);
            if (!$newfieldid) {
                $update->sourcetype = 'upload';
                $update->sourcecmid = 0;
                $update->sourcefieldid = 0;
            } else {
                $update->sourcefieldid = $newfieldid;
            }
        }
        $DB->update_record('vimigallery', $update);
    }
}
