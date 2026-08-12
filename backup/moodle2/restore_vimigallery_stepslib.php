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
}
