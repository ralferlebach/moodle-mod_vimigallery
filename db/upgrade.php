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

    if ($oldversion < 2026081105) {
        $table = new xmldb_table('vimigallery');
        $field = new xmldb_field('sourcemode', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'reference', 'freshness');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2026081105, 'vimigallery');
    }

    if ($oldversion < 2026081111) {
        // Add the comment and completion settings to the instance.
        $table = new xmldb_table('vimigallery');
        $field = new xmldb_field('allowcomments', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'sourcemode');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field(
            'completioncommentsmin',
            XMLDB_TYPE_INTEGER,
            '10',
            null,
            XMLDB_NOTNULL,
            null,
            '0',
            'allowcomments'
        );
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add a content hash to items (nullable: char columns take no empty default).
        $table = new xmldb_table('vimigallery_item');
        $field = new xmldb_field('contenthash', XMLDB_TYPE_CHAR, '40', null, null, null, null, 'authorname');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            $rs = $DB->get_recordset('vimigallery_item', null, '', 'id, mapjson');
            foreach ($rs as $item) {
                $DB->set_field('vimigallery_item', 'contenthash', sha1((string) $item->mapjson), ['id' => $item->id]);
            }
            $rs->close();
        }

        // Create the comment table.
        $table = new xmldb_table('vimigallery_comment');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('galleryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('content', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
            $table->add_field('format', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_key('galleryid', XMLDB_KEY_FOREIGN, ['galleryid'], 'vimigallery', ['id']);
            $table->add_key('itemid', XMLDB_KEY_FOREIGN, ['itemid'], 'vimigallery_item', ['id']);
            $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
            $table->add_index('itemid-timecreated', XMLDB_INDEX_NOTUNIQUE, ['itemid', 'timecreated']);
            $dbman->create_table($table);
        }

        upgrade_mod_savepoint(true, 2026081111, 'vimigallery');
    }

    if ($oldversion < 2026081112) {
        $table = new xmldb_table('vimigallery');
        $field = new xmldb_field(
            'enablecompare',
            XMLDB_TYPE_INTEGER,
            '1',
            null,
            XMLDB_NOTNULL,
            null,
            '0',
            'completioncommentsmin'
        );
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2026081112, 'vimigallery');
    }

    return true;
}
