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

namespace mod_vimigallery\completion;

use core_completion\activity_custom_completion;

/**
 * Custom completion rules for mod_vimigallery.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_completion extends activity_custom_completion {
    /**
     * The completion state for a rule.
     *
     * @param string $rule The rule name.
     * @return int COMPLETION_COMPLETE or COMPLETION_INCOMPLETE.
     */
    public function get_state(string $rule): int {
        global $DB;
        $this->validate_rule($rule);

        $min = (int) $DB->get_field('vimigallery', 'completioncommentsmin', ['id' => $this->cm->instance]);
        if ($min <= 0) {
            return COMPLETION_INCOMPLETE;
        }
        $count = \mod_vimigallery\local\comment_service::count_for_user($this->cm->instance, $this->userid);
        return $count >= $min ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
    }

    /**
     * The custom rules this module defines.
     *
     * @return array The rule names.
     */
    public static function get_defined_custom_rules(): array {
        return ['completioncommentsmin'];
    }

    /**
     * Human-readable descriptions of the rules.
     *
     * @return array Map of rule name => description.
     */
    public function get_custom_rule_descriptions(): array {
        $min = (int) ($this->cm->customdata['customcompletionrules']['completioncommentsmin'] ?? 0);
        return [
            'completioncommentsmin' => get_string('completiondetail:comments', 'mod_vimigallery', $min),
        ];
    }

    /**
     * The order rules are shown and evaluated in.
     *
     * @return array The ordered rule names.
     */
    public function get_sort_order(): array {
        return ['completioncommentsmin'];
    }
}
