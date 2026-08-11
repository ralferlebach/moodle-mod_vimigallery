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
 * Drag-and-drop reordering for the gallery arrange page.
 *
 * The list stays fully usable without JavaScript through the up/down links; this
 * module adds drag-and-drop on top and persists the new order via a web service.
 *
 * @module     mod_vimigallery/arrange
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import SortableList from 'core/sortable_list';
import jQuery from 'jquery';
import Ajax from 'core/ajax';
import Notification from 'core/notification';

/**
 * Initialise drag-and-drop reordering.
 *
 * @param {number} cmid The gallery course module id.
 * @return {void}
 */
export const init = (cmid) => {
    const selector = '.vimigallery-arrange-list';
    const list = document.querySelector(selector);
    if (!list) {
        return;
    }

    new SortableList(selector, {
        moveHandlerSelector: '.vimigallery-drag-handle',
    });

    jQuery(selector).children().on('sortablelist-drop', () => {
        // Defer so the DOM reflects the final position before we read the order.
        window.setTimeout(() => {
            const itemids = [];
            list.querySelectorAll('[data-itemid]').forEach((el) => {
                itemids.push(parseInt(el.getAttribute('data-itemid'), 10));
            });
            Ajax.call([{
                methodname: 'mod_vimigallery_reorder',
                args: {cmid: cmid, itemids: itemids},
            }])[0].fail(Notification.exception);
        }, 0);
    });
};
