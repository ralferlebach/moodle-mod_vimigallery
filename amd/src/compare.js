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
 * Side-by-side comparison of two ViMi Pad maps.
 *
 * Both maps are mounted read-only through mod_vimipad's embeddable editor. An
 * optional toggle couples the two panes' scrolling so they move together.
 *
 * @module     mod_vimigallery/compare
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


import {coupleScroll} from 'mod_vimigallery/logic';

/**
 * Resolve a mod_vimipad editor string from the preloaded strings.
 *
 * @param {string} key The string key.
 * @return {string|undefined} The localised string, or undefined.
 */
const getString = (key) => {
    const store = window.M && window.M.str && window.M.str.mod_vimipad;
    return store && store[key] !== undefined ? store[key] : undefined;
};

/**
 * Initialise a comparison view.
 *
 * @param {string} rootId The id of the comparison root element.
 * @return {void}
 */
export const init = (rootId) => {
    const root = document.getElementById(rootId);
    if (!root) {
        return;
    }

    let formconfigs = {};
    const configEl = document.getElementById(rootId + '_formconfigs');
    if (configEl) {
        try {
            formconfigs = JSON.parse(configEl.textContent) || {};
        } catch (e) {
            formconfigs = {};
        }
    }

    const panes = Array.prototype.slice.call(root.querySelectorAll('[data-compare-side]'));
    panes.forEach((container) => {
        const input = document.getElementById(container.getAttribute('data-input'));
        if (!input) {
            return;
        }
        const profile = container.getAttribute('data-profile');
        require(['mod_vimipad/editor_lazy'], (editor) => {
            editor.mountValue(container, {
                value: input.value || '',
                onChange: () => {
                    // Read-only: nothing is written back.
                },
                profile: profile,
                readonly: true,
                formconfig: formconfigs[profile],
                initialView: 'canvas',
                showViewToggle: false,
                getString: getString,
            });
        });
    });

    const toggle = root.querySelector('[data-compare-couple]');
    if (toggle && panes.length === 2) {
        toggle.addEventListener('change', () => {
            if (toggle.checked) {
                coupleScroll(panes[0], panes[1]);
                toggle.disabled = true;
            }
        });
    }
};
