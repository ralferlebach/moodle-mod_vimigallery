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
 * Upgrade steps for mod_vimigallery.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute mod_vimigallery upgrade from the given old version.
 *
 * @param int $oldversion The version we are upgrading from.
 * @return bool Always true on success.
 */
function xmldb_vimigallery_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2026081104) {
        $table = new xmldb_table('vimigallery');

        $fields = [
            new xmldb_field('sourcetype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'upload', 'showauthors'),
            new xmldb_field('sourcecmid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'sourcetype'),
            new xmldb_field('sourcefieldid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'sourcecmid'),
            new xmldb_field('freshness', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, 'live', 'sourcefieldid'),
        ];
        foreach ($fields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }

        upgrade_mod_savepoint(true, 2026081104, 'vimigallery');
    }

    return true;
}
