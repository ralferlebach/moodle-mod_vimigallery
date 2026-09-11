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
        $userinfo = $this->get_setting_value('userinfo');

        $gallery = new backup_nested_element('vimigallery', ['id'], [
            'name', 'intro', 'introformat', 'displaymode', 'showtabs',
            'showauthors', 'sourcetype', 'sourcecmid', 'sourcefieldid', 'freshness',
            'sourcemode', 'allowcomments', 'completioncommentsmin', 'enablecompare', 'timemodified',
        ]);
        $items = new backup_nested_element('items');
        $item = new backup_nested_element('item', ['id'], [
            'sortorder', 'visible', 'sourcetype', 'profile', 'mapjson',
            'authorname', 'contenthash', 'sourceuserid', 'sourcekey', 'timecreated',
        ]);
        $contributors = new backup_nested_element('contributors');
        $contributor = new backup_nested_element('contributor', ['id'], ['userid']);
        $comments = new backup_nested_element('comments');
        $comment = new backup_nested_element('comment', ['id'], [
            'userid', 'content', 'format', 'timecreated', 'timemodified',
        ]);

        $gallery->add_child($items);
        $items->add_child($item);
        $item->add_child($contributors);
        $contributors->add_child($contributor);
        $item->add_child($comments);
        $comments->add_child($comment);

        $gallery->set_source_table('vimigallery', ['id' => backup::VAR_ACTIVITYID]);

        // Items materialised from another activity are copies of learners' work
        // and carry their names, so they are user data and must not travel in a
        // backup taken without user information. Uploaded items are teacher
        // content and are always included.
        if ($userinfo) {
            $item->set_source_table('vimigallery_item', ['galleryid' => backup::VAR_PARENTID]);
        } else {
            $item->set_source_sql(
                "SELECT * FROM {vimigallery_item} WHERE galleryid = ? AND sourcetype = ?",
                ['galleryid' => backup::VAR_PARENTID, 'sourcetype' => backup_helper::is_sqlparam('upload')]
            );
        }

        if ($userinfo) {
            $contributor->set_source_table('vimigallery_item_user', ['itemid' => backup::VAR_PARENTID]);
            $contributor->annotate_ids('user', 'userid');
            $comment->set_source_table('vimigallery_comment', ['itemid' => backup::VAR_PARENTID]);
            $comment->annotate_ids('user', 'userid');
        }

        $gallery->annotate_files('mod_vimigallery', 'intro', null);
        $gallery->annotate_files('mod_vimigallery', 'source', null);

        return $this->prepare_activity_structure($gallery);
    }
}
