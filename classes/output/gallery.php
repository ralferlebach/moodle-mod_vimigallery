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
 * Renderable for one gallery: its settings and the maps it shows.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gallery implements renderable {
    /** @var cm_info The course module. */
    public cm_info $cm;

    /** @var \stdClass The gallery instance record. */
    public \stdClass $instance;

    /**
     * Constructor.
     *
     * @param cm_info $cm The course module.
     */
    public function __construct(cm_info $cm) {
        global $DB;
        $this->cm = $cm;
        $this->instance = $DB->get_record('vimigallery', ['id' => $cm->instance], '*', MUST_EXIST);
    }

    /**
     * The visible items in display order.
     *
     * @return \stdClass[] The item records.
     */
    public function get_items(): array {
        global $DB;

        // A live datafield source is read fresh, per viewer, honouring the source
        // database's access rules. Uploads and materialised (static/snapshot)
        // sources come from the stored items.
        if ($this->instance->freshness === 'live') {
            $source = vimigallery_make_source($this->instance);
            if ($source !== null) {
                return $source->get_items();
            }
        }

        return array_values($DB->get_records(
            'vimigallery_item',
            ['galleryid' => $this->instance->id, 'visible' => 1],
            'sortorder ASC'
        ));
    }

    /**
     * Whether the map/list tabs should be shown.
     *
     * @return bool True to show tabs.
     */
    public function show_tabs(): bool {
        return !empty($this->instance->showtabs);
    }

    /**
     * Whether author names should be shown.
     *
     * @return bool True to show authors.
     */
    public function show_authors(): bool {
        return !empty($this->instance->showauthors);
    }

    /**
     * Whether commenting is enabled on this gallery.
     *
     * @return bool True if learners may comment.
     */
    public function allow_comments(): bool {
        return !empty($this->instance->allowcomments);
    }

    /**
     * Whether the current user may post comments here.
     *
     * @return bool True if the user has the comment capability.
     */
    public function can_comment(): bool {
        return $this->allow_comments()
            && has_capability('mod/vimigallery:comment', \context_module::instance($this->cm->id));
    }

    /**
     * The course module id of this gallery.
     *
     * @return int The cmid.
     */
    public function cmid(): int {
        return (int) $this->cm->id;
    }

    /**
     * Whether the side-by-side compare view is offered.
     *
     * @return bool True if compare is enabled.
     */
    public function enable_compare(): bool {
        return !empty($this->instance->enablecompare);
    }
}
