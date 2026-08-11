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
 * The main mod_vimigallery configuration form.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Module instance settings form.
 */
class mod_vimigallery_mod_form extends moodleform_mod {
    /**
     * Define the form.
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), ['size' => 64]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements();

        // Sources.
        $mform->addElement('header', 'sourceheader', get_string('sourceheader', 'mod_vimigallery'));
        $mform->setExpanded('sourceheader');
        $mform->addElement(
            'filemanager',
            'vimijson',
            get_string('sourcefiles', 'mod_vimigallery'),
            null,
            ['subdirs' => 0, 'maxfiles' => 200, 'accepted_types' => ['.json']]
        );
        $mform->addHelpButton('vimijson', 'sourcefiles', 'mod_vimigallery');

        // Display options.
        $mform->addElement('header', 'displayheader', get_string('displayheader', 'mod_vimigallery'));
        $mform->setExpanded('displayheader');

        $mform->addElement('select', 'displaymode', get_string('displaymode', 'mod_vimigallery'), [
            'page' => get_string('displaymode_page', 'mod_vimigallery'),
            'course' => get_string('displaymode_course', 'mod_vimigallery'),
        ]);
        $mform->setDefault('displaymode', 'page');
        $mform->addHelpButton('displaymode', 'displaymode', 'mod_vimigallery');

        $mform->addElement('advcheckbox', 'showtabs', get_string('showtabs', 'mod_vimigallery'));
        $mform->setDefault('showtabs', 1);
        $mform->addHelpButton('showtabs', 'showtabs', 'mod_vimigallery');

        $mform->addElement('advcheckbox', 'showauthors', get_string('showauthors', 'mod_vimigallery'));
        $mform->setDefault('showauthors', 0);
        $mform->addHelpButton('showauthors', 'showauthors', 'mod_vimigallery');

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Load the stored source files into the file manager for editing.
     *
     * @param array $defaultvalues The default values to prepare.
     * @return void
     */
    public function data_preprocessing(&$defaultvalues) {
        if ($this->current && !empty($this->current->coursemodule)) {
            $context = context_module::instance($this->current->coursemodule);
            $draftitemid = file_get_submitted_draft_itemid('vimijson');
            file_prepare_draft_area(
                $draftitemid,
                $context->id,
                'mod_vimigallery',
                'source',
                0,
                ['subdirs' => 0, 'maxfiles' => 200, 'accepted_types' => ['.json']]
            );
            $defaultvalues['vimijson'] = $draftitemid;
        }
    }
}
