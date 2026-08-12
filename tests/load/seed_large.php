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
 * CLI seed for the mod_vimigallery JMeter/k6 load test.
 *
 * Creates a course with a gallery holding a large album of materialised maps and
 * a student, then enables the REST web service, adds the gallery's read function
 * and mints a token. Finally it prints the shell exports the load run needs.
 *
 * The gallery's load profile is the album: the page ships only its first map and
 * fetches the rest through mod_vimigallery_get_item, so the interesting question
 * is what that per-map fetch costs under concurrency, and whether it stays flat
 * as the album grows.
 *
 * Usage: php mod/vimigallery/tests/load/seed_large.php [items] [nodes_per_map]
 *   items          number of maps in the album (default 100)
 *   nodes_per_map  nodes in each map (default 150)
 *
 * Intended for a disposable dev/staging site — never point it at production.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir . '/enrollib.php');
require_once($CFG->libdir . '/externallib.php');

$items = isset($argv[1]) ? max(1, (int) $argv[1]) : 100;
$nodecount = isset($argv[2]) ? max(1, (int) $argv[2]) : 150;
$now = time();

// Course.
$course = create_course((object) [
    'fullname' => 'ViMi Gallery load ' . $now,
    'shortname' => 'vgload' . $now,
    'category' => 1,
    'summaryformat' => FORMAT_HTML,
]);

// Student user, enrolled.
$username = 'vgal_load_' . $now;
$user = (object) [
    'username' => $username,
    'auth' => 'manual',
    'confirmed' => 1,
    'firstname' => 'Gallery',
    'lastname' => 'Load',
    'email' => $username . '@example.invalid',
    'mnethostid' => $CFG->mnet_localhost_id,
];
$user->id = user_create_user($user, false, false);
$password = 'Vimi!load_1';
update_internal_user_password($DB->get_record('user', ['id' => $user->id]), $password);

$context = context_course::instance($course->id);
$studentrole = $DB->get_record('role', ['archetype' => 'student'], '*', MUST_EXIST);
$manual = enrol_get_plugin('manual');
$enrol = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual'], '*', MUST_EXIST);
$manual->enrol_user($enrol, $user->id, $studentrole->id);

// Gallery activity, upload source (items are materialised directly below).
$module = $DB->get_record('modules', ['name' => 'vimigallery'], '*', MUST_EXIST);
$moduleinfo = (object) [
    'modulename' => 'vimigallery',
    'module' => $module->id,
    'course' => $course->id,
    'section' => 0,
    'visible' => 1,
    'name' => 'Load album',
    'intro' => '',
    'introformat' => FORMAT_HTML,
    'displaymode' => 'page',
    'showtabs' => 1,
    'showauthors' => 1,
    'sourcetype' => 'upload',
    'freshness' => 'live',
    'sourcemode' => 'reference',
    'allowcomments' => 1,
    'enablecompare' => 1,
    'completioncommentsmin' => 0,
];
$created = add_moduleinfo($moduleinfo, $course);
$cmid = (int) $created->coursemodule;
$galleryid = (int) $created->instance;

// Build one map document of the requested size, then materialise the album.
$makemap = function (int $index) use ($nodecount): string {
    $nodes = [];
    $relations = [];
    for ($i = 0; $i < $nodecount; $i++) {
        $nodes[] = ['stableid' => 'n' . $i, 'label' => 'Map' . $index . ' N' . $i];
    }
    for ($i = 0; $i < $nodecount - 1; $i++) {
        $relations[] = [
            'stableid' => 'r' . $i,
            'sourceid' => 'n' . $i,
            'targetid' => 'n' . ($i + 1),
            'label' => 'links',
        ];
    }
    return (string) json_encode([
        'profile' => 'conceptmap',
        'nodes' => $nodes,
        'relations' => $relations,
    ]);
};

$rows = [];
$firstmap = '';
for ($i = 0; $i < $items; $i++) {
    $map = $makemap($i);
    if ($i === 0) {
        $firstmap = $map;
    }
    $rows[] = (object) [
        'galleryid' => $galleryid,
        'sortorder' => $i,
        'visible' => 1,
        'sourcetype' => 'upload',
        'profile' => 'conceptmap',
        'mapjson' => $map,
        'authorname' => 'Author ' . $i,
        'contenthash' => sha1($map),
        'sourceuserid' => null,
        'timecreated' => $now,
    ];
}
$DB->insert_records('vimigallery_item', $rows);

// A comment on every item, so the page's comment loader is exercised at scale
// (this is the path that used to cost two queries per map).
$itemids = $DB->get_fieldset_select('vimigallery_item', 'id', 'galleryid = ?', [$galleryid]);
$commentrows = [];
foreach ($itemids as $itemid) {
    $commentrows[] = (object) [
        'galleryid' => $galleryid,
        'itemid' => $itemid,
        'userid' => $user->id,
        'content' => 'Load comment',
        'format' => FORMAT_PLAIN,
        'timecreated' => $now,
        'timemodified' => $now,
    ];
}
$DB->insert_records('vimigallery_comment', $commentrows);

// Enable web services + REST.
set_config('enablewebservices', 1);
$protocols = get_config('core', 'webserviceprotocols');
if (strpos((string) $protocols, 'rest') === false) {
    set_config('webserviceprotocols', trim($protocols . ',rest', ','));
}

// Allow REST use for authenticated users so the minted token can be used
// (dev/test convenience; do not do this on a production site).
$authrole = $DB->get_record('role', ['shortname' => 'user'], '*', IGNORE_MISSING);
if ($authrole) {
    assign_capability('webservice/rest:use', CAP_ALLOW, $authrole->id, context_system::instance()->id, true);
    context_system::instance()->mark_dirty();
}

// External service carrying the gallery's read function, then a token.
$readfunctions = ['mod_vimigallery_get_item'];
$service = (object) [
    'name' => 'ViMi Gallery load ' . $now,
    'shortname' => 'vimigalleryload' . $now,
    'enabled' => 1,
    'restrictedusers' => 0,
    'downloadfiles' => 0,
    'uploadfiles' => 0,
    'timecreated' => $now,
    'timemodified' => $now,
];
$service->id = $DB->insert_record('external_services', $service);
foreach ($readfunctions as $fn) {
    $DB->insert_record('external_services_functions', (object) [
        'externalserviceid' => $service->id,
        'functionname' => $fn,
    ]);
}
$token = external_generate_token(
    EXTERNAL_TOKEN_PERMANENT,
    $service->id,
    $user->id,
    context_system::instance()
);

// Item ids the load run cycles through. JMeter reads them from a CSV data set
// so the threads spread across the album instead of all hitting one row; k6
// takes the same list through the ITEMIDS environment variable.
$idlist = implode(',', array_map('intval', $itemids));
file_put_contents(__DIR__ . '/itemids.csv', implode("
", array_map('intval', $itemids)) . "
");

echo "export BASE_URL='{$CFG->wwwroot}'\n";
echo "export TOKEN='{$token}'\n";
echo "export CMID='{$cmid}'\n";
echo "export ITEMIDS='{$idlist}'\n";
echo "export USERNAME='{$username}'\n";
echo "export PASSWORD='{$password}'\n";
echo "# Album: {$items} maps x {$nodecount} nodes; one comment per map.\n";
echo "# First map is inlined in the page (" . strlen($firstmap) . " bytes); the rest are fetched.\n";
echo "# Run: make jmeter  (or: make load-k6)\n";
