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

namespace mod_vimigallery\local;

/**
 * Curation of a gallery's materialised items: order and visibility.
 *
 * Operates on the stored vimigallery_item rows (uploads and materialised
 * static/snapshot sources). Live sources have no stored items and are not
 * curated here.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class curation {
    /** Move an item towards the start. */
    public const UP = 'up';

    /** Move an item towards the end. */
    public const DOWN = 'down';

    /**
     * Move an item one step up or down within its gallery.
     *
     * @param int $galleryid The gallery instance id.
     * @param int $itemid The item to move.
     * @param string $direction One of self::UP or self::DOWN.
     * @return void
     */
    public static function move(int $galleryid, int $itemid, string $direction): void {
        global $DB;

        $items = array_values($DB->get_records(
            'vimigallery_item',
            ['galleryid' => $galleryid],
            'sortorder ASC, id ASC'
        ));

        $index = null;
        foreach ($items as $i => $item) {
            if ((int) $item->id === $itemid) {
                $index = $i;
                break;
            }
        }
        if ($index === null) {
            return;
        }

        $swap = $direction === self::UP ? $index - 1 : $index + 1;
        if ($swap < 0 || $swap >= count($items)) {
            return;
        }

        $tmp = $items[$index];
        $items[$index] = $items[$swap];
        $items[$swap] = $tmp;

        self::write_order($items);
    }

    /**
     * Set an item's visibility, guarding that it belongs to the gallery.
     *
     * @param int $galleryid The gallery instance id.
     * @param int $itemid The item to change.
     * @param bool $visible Whether the item is shown to learners.
     * @return void
     */
    public static function set_visible(int $galleryid, int $itemid, bool $visible): void {
        global $DB;
        if (!$DB->record_exists('vimigallery_item', ['id' => $itemid, 'galleryid' => $galleryid])) {
            return;
        }
        $DB->set_field('vimigallery_item', 'visible', $visible ? 1 : 0, ['id' => $itemid]);
    }

    /**
     * Renumber a gallery's items to a contiguous 0..n-1 order.
     *
     * @param int $galleryid The gallery instance id.
     * @return void
     */
    public static function normalise(int $galleryid): void {
        global $DB;
        $items = array_values($DB->get_records(
            'vimigallery_item',
            ['galleryid' => $galleryid],
            'sortorder ASC, id ASC'
        ));
        self::write_order($items);
    }

    /**
     * Persist an explicit item order given a list of item ids.
     *
     * Only ids belonging to the gallery are honoured; any of the gallery's items
     * not listed are appended after, keeping their relative order.
     *
     * @param int $galleryid The gallery instance id.
     * @param int[] $itemids The desired item order.
     * @return void
     */
    public static function set_order(int $galleryid, array $itemids): void {
        global $DB;
        $existing = $DB->get_records(
            'vimigallery_item',
            ['galleryid' => $galleryid],
            'sortorder ASC, id ASC'
        );
        $ordered = [];
        foreach ($itemids as $id) {
            $id = (int) $id;
            if (isset($existing[$id])) {
                $ordered[$id] = $existing[$id];
            }
        }
        foreach ($existing as $id => $row) {
            if (!isset($ordered[$id])) {
                $ordered[$id] = $row;
            }
        }
        self::write_order(array_values($ordered));
    }

    /**
     * Persist the given item ordering as contiguous sort orders.
     *
     * @param \stdClass[] $items The items in the desired order.
     * @return void
     */
    protected static function write_order(array $items): void {
        global $DB;
        foreach ($items as $order => $item) {
            if ((int) $item->sortorder !== $order) {
                $DB->set_field('vimigallery_item', 'sortorder', $order, ['id' => $item->id]);
            }
        }
    }
}
