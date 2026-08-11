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
 * Backup structure step for mod_vimigallery.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the complete gallery structure for backup.
 */
class backup_vimigallery_activity_structure_step extends backup_activity_structure_step {
    /**
     * Define the backup structure.
     *
     * @return backup_nested_element The root element.
     */
    protected function define_structure() {
        $gallery = new backup_nested_element('vimigallery', ['id'], [
            'name', 'intro', 'introformat', 'displaymode', 'showtabs',
            'showauthors', 'timemodified',
        ]);
        $items = new backup_nested_element('items');
        $item = new backup_nested_element('item', ['id'], [
            'sortorder', 'visible', 'sourcetype', 'profile', 'mapjson',
            'authorname', 'timecreated',
        ]);

        $gallery->add_child($items);
        $items->add_child($item);

        $gallery->set_source_table('vimigallery', ['id' => backup::VAR_ACTIVITYID]);
        $item->set_source_table('vimigallery_item', ['galleryid' => backup::VAR_PARENTID]);

        $gallery->annotate_files('mod_vimigallery', 'intro', null);
        $gallery->annotate_files('mod_vimigallery', 'source', null);

        return $this->prepare_activity_structure($gallery);
    }
}
