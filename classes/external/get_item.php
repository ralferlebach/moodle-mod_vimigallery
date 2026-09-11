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

namespace mod_vimigallery\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * External function returning the map of a single gallery item.
 *
 * The gallery page ships only its first map inline; the rest are fetched through
 * this function as the viewer reaches them. Otherwise every map in an album would
 * be loaded from the database, held in PHP, escaped and written into the HTML on
 * every page view, even though only one is ever on screen.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_item extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters The parameter definition.
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'The gallery course module id'),
            'itemid' => new external_value(PARAM_ALPHANUMEXT, 'The item id (numeric, or synthetic for live sources)'),
        ]);
    }

    /**
     * Return one item's map.
     *
     * @param int $cmid The gallery course module id.
     * @param string $itemid The item id.
     * @return array The item's map and profile.
     */
    public static function execute(int $cmid, string $itemid): array {
        $params = self::validate_parameters(self::execute_parameters(), [
            'cmid' => $cmid,
            'itemid' => $itemid,
        ]);

        $cm = get_coursemodule_from_id('vimigallery', $params['cmid'], 0, false, MUST_EXIST);
        $context = \context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/vimigallery:view', $context);

        // Resolve through the same renderable the page uses, so visibility,
        // freshness and the per-viewer security of live sources all apply here
        // exactly as they do when the page is built.
        $course = get_course($cm->course);
        $cminfo = get_fast_modinfo($course)->get_cm($cm->id);
        $gallery = new \mod_vimigallery\output\gallery($cminfo);

        foreach ($gallery->get_items() as $item) {
            if ((string) $item->id === (string) $params['itemid']) {
                return [
                    'itemid' => (string) $item->id,
                    'profile' => (string) $item->profile,
                    'mapjson' => (string) $item->mapjson,
                ];
            }
        }

        throw new \moodle_exception('invaliditem', 'mod_vimigallery');
    }

    /**
     * Return value.
     *
     * @return external_single_structure The return definition.
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'itemid' => new external_value(PARAM_ALPHANUMEXT, 'The item id'),
            'profile' => new external_value(PARAM_ALPHANUMEXT, 'The diagram profile'),
            'mapjson' => new external_value(PARAM_RAW, 'The serialised map'),
        ]);
    }
}
