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
use core_external\external_multiple_structure;

/**
 * External function to persist a curated item order.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reorder extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters The parameter definition.
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'The gallery course module id'),
            'itemids' => new external_multiple_structure(
                new external_value(PARAM_INT, 'A gallery item id'),
                'The item ids in the desired order'
            ),
        ]);
    }

    /**
     * Persist the given order.
     *
     * @param int $cmid The gallery course module id.
     * @param int[] $itemids The item ids in the desired order.
     * @return array The status result.
     */
    public static function execute(int $cmid, array $itemids): array {
        $params = self::validate_parameters(self::execute_parameters(), [
            'cmid' => $cmid,
            'itemids' => $itemids,
        ]);

        $cm = get_coursemodule_from_id('vimigallery', $params['cmid'], 0, false, MUST_EXIST);
        $context = \context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/vimigallery:manageitems', $context);

        \mod_vimigallery\local\curation::set_order($cm->instance, $params['itemids']);

        return ['status' => true];
    }

    /**
     * Return value.
     *
     * @return external_single_structure The return definition.
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'Whether the order was saved'),
        ]);
    }
}
