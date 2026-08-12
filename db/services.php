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
 * External function definitions for mod_vimigallery.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'mod_vimigallery_reorder' => [
        'classname' => 'mod_vimigallery\external\reorder',
        'methodname' => 'execute',
        'description' => 'Persist a curated order of the maps in a gallery.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'mod/vimigallery:manageitems',
    ],
    'mod_vimigallery_post_comment' => [
        'classname' => 'mod_vimigallery\external\post_comment',
        'methodname' => 'execute',
        'description' => 'Post a comment on a map in a gallery.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'mod/vimigallery:comment',
    ],
    'mod_vimigallery_get_item' => [
        'classname' => 'mod_vimigallery\external\get_item',
        'methodname' => 'execute',
        'description' => 'Fetch the map of a single gallery item for lazy loading.',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'mod/vimigallery:view',
    ],
];
