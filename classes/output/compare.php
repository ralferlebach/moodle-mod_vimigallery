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

namespace mod_vimigallery\output;

use cm_info;
use renderable;

/**
 * Renderable for the side-by-side map comparison view.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class compare implements renderable {
    /** @var cm_info The course module. */
    protected $cm;

    /** @var \stdClass The gallery instance. */
    public $instance;

    /** @var array All gallery items (for the selectors). */
    protected $items;

    /** @var \stdClass|null The left-hand item. */
    protected $left;

    /** @var \stdClass|null The right-hand item. */
    protected $right;

    /**
     * Build the comparison from the chosen item ids.
     *
     * @param cm_info $cm The gallery course module.
     * @param string $leftid The id of the left item (numeric or synthetic).
     * @param string $rightid The id of the right item.
     */
    public function __construct(cm_info $cm, string $leftid = '', string $rightid = '') {
        $gallery = new gallery($cm);
        $this->cm = $cm;
        $this->instance = $gallery->instance;
        $this->items = $gallery->get_items();
        $this->left = $this->find($leftid);
        $this->right = $this->find($rightid);
    }

    /**
     * Resolve an item by its id within this gallery.
     *
     * @param string $id The requested item id.
     * @return \stdClass|null The item, or null if not found.
     */
    protected function find(string $id) {
        if ($id === '') {
            return null;
        }
        foreach ($this->items as $item) {
            if ((string) $item->id === $id) {
                return $item;
            }
        }
        return null;
    }

    /**
     * All items, for building the selector dropdowns.
     *
     * @return array The gallery items.
     */
    public function get_items(): array {
        return $this->items;
    }

    /**
     * The chosen left item.
     *
     * @return \stdClass|null The left item.
     */
    public function get_left() {
        return $this->left;
    }

    /**
     * The chosen right item.
     *
     * @return \stdClass|null The right item.
     */
    public function get_right() {
        return $this->right;
    }

    /**
     * The course module id.
     *
     * @return int The cmid.
     */
    public function cmid(): int {
        return (int) $this->cm->id;
    }

    /**
     * The similarity of the two chosen maps as a fraction (0.0-1.0).
     *
     * Uses mod_vimipad's public scoring facade with token matching. Returns null
     * when either side is missing or the maps cannot be scored (for example when
     * the reference scorer is unavailable or a map is not snapshot-shaped).
     *
     * @return float|null The similarity fraction, or null.
     */
    public function similarity(): ?float {
        if ($this->left === null || $this->right === null) {
            return null;
        }
        if (!class_exists('\\mod_vimipad\\api\\score')) {
            return null;
        }
        return \mod_vimipad\api\score::fraction(
            (string) $this->right->mapjson,
            (string) $this->left->mapjson,
            'reference',
            \mod_vimipad\api\score::MATCH_TOKEN
        );
    }
}
