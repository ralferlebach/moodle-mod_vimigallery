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
        global $COURSE;
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

        $mform->addElement('select', 'sourcetype', get_string('sourcetype', 'mod_vimigallery'), [
            'upload' => get_string('source_upload', 'mod_vimigallery'),
            'datafield' => get_string('source_datafield', 'mod_vimigallery'),
            'qtype' => get_string('source_qtype', 'mod_vimigallery'),
        ]);
        $mform->setDefault('sourcetype', 'upload');
        $mform->addHelpButton('sourcetype', 'sourcetype', 'mod_vimigallery');

        // Upload source: one or more exported JSON maps.
        $mform->addElement(
            'filemanager',
            'vimijson',
            get_string('sourcefiles', 'mod_vimigallery'),
            null,
            ['subdirs' => 0, 'maxfiles' => 200, 'accepted_types' => ['.json']]
        );
        $mform->addHelpButton('vimijson', 'sourcefiles', 'mod_vimigallery');
        $mform->hideIf('vimijson', 'sourcetype', 'neq', 'upload');

        // Datafield source: a ViMi Pad field of a Database activity in this course.
        $datafieldsources = vimigallery_list_datafield_sources($COURSE->id);
        if (empty($datafieldsources)) {
            $datafieldsources = ['' => get_string('nodatafields', 'mod_vimigallery')];
        }
        $mform->addElement(
            'select',
            'datafieldsource',
            get_string('datafieldsource', 'mod_vimigallery'),
            $datafieldsources
        );
        $mform->addHelpButton('datafieldsource', 'datafieldsource', 'mod_vimigallery');
        $mform->hideIf('datafieldsource', 'sourcetype', 'neq', 'datafield');

        // Qtype source: a Quiz activity containing ViMi Pad questions.
        $quizsources = vimigallery_list_quiz_sources($COURSE->id);
        if (empty($quizsources)) {
            $quizsources = ['' => get_string('noquizzes', 'mod_vimigallery')];
        }
        $mform->addElement('select', 'qtypesource', get_string('qtypesource', 'mod_vimigallery'), $quizsources);
        $mform->addHelpButton('qtypesource', 'qtypesource', 'mod_vimigallery');
        $mform->hideIf('qtypesource', 'sourcetype', 'neq', 'qtype');

        $mform->addElement('select', 'sourcemode', get_string('sourcemode', 'mod_vimigallery'), [
            'reference' => get_string('sourcemode_reference', 'mod_vimigallery'),
        ]);
        $mform->setDefault('sourcemode', 'reference');
        $mform->addHelpButton('sourcemode', 'sourcemode', 'mod_vimigallery');
        $mform->hideIf('sourcemode', 'sourcetype', 'neq', 'qtype');

        $mform->addElement('select', 'freshness', get_string('freshness', 'mod_vimigallery'), [
            'live' => get_string('freshness_live', 'mod_vimigallery'),
            'static' => get_string('freshness_static', 'mod_vimigallery'),
            'snapshot' => get_string('freshness_snapshot', 'mod_vimigallery'),
        ]);
        $mform->setDefault('freshness', 'live');
        $mform->addHelpButton('freshness', 'freshness', 'mod_vimigallery');
        $mform->hideIf('freshness', 'sourcetype', 'eq', 'upload');

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
        if (!empty($this->current->sourcecmid) && !empty($this->current->sourcefieldid)) {
            $defaultvalues['datafieldsource'] =
                $this->current->sourcecmid . ':' . $this->current->sourcefieldid;
        }
        if (!empty($this->current->sourcecmid) && ($this->current->sourcetype ?? '') === 'qtype') {
            $defaultvalues['qtypesource'] = $this->current->sourcecmid;
        }
    }
}
