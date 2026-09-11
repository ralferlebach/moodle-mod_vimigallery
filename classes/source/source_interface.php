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

namespace mod_vimigallery\source;

/**
 * A source of ViMi maps for a gallery.
 *
 * An implementation turns some origin (an upload set, a database field, a
 * question, a ViMi Pad activity) into a normalised, ordered list of maps. Every
 * implementation is responsible for enforcing the origin's own access rules
 * (capability, group mode, entry visibility) so that a gallery never exposes a
 * map the viewer could not otherwise see.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface source_interface {
    /**
     * Upper bound on the items a single source may materialise or display.
     *
     * A gallery renders every item into one page, and some sources have to do
     * per-record work (loading a question usage, for instance) that cannot be
     * batched away. Without a ceiling a cohort-sized quiz or database would turn
     * one page request into thousands of queries. Sources take the most recent
     * items up to this bound.
     */
    const MAX_ITEMS = 200;
    /**
     * The maps this source contributes, as the viewer is allowed to see them.
     *
     * Each returned item is an stdClass with at least: `mapjson` (string),
     * `profile` (string), `authorname` (string), `sortorder` (int),
     * `visible` (int) and a stable `id` (int|string) unique within the source.
     *
     * @param int|null $userid The viewer, or null for the current user.
     * @return \stdClass[] The visible maps, in display order.
     */
    public function get_items(?int $userid = null): array;
}
