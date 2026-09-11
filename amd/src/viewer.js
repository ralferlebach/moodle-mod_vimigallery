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
 * Album viewer for a ViMi Pad gallery.
 *
 * Each map is a frozen value rendered read-only through mod_vimipad's embeddable
 * editor (mountValue). Maps form a swipeable album: one is shown at a time, and
 * each map's editor is mounted only when it (or its neighbour) becomes active, so
 * a large album stays light.
 *
 * @module     mod_vimigallery/viewer
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


import Ajax from 'core/ajax';
import {buildCommentElement} from 'mod_vimigallery/logic';
import Notification from 'core/notification';

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
 * Mount one map into its container (read-only). Idempotent per mount descriptor.
 *
 * @param {object} mount The mount descriptor.
 * @param {boolean} showtabs Whether the map/list toggle is enabled.
 * @param {number} cmid The gallery course module id, for fetching lazy maps.
 * @return {void}
 */
const mountOne = (mount, showtabs, cmid) => {
    if (!mount || mount.mounted) {
        return;
    }
    const container = document.getElementById(mount.containerid);
    const input = document.getElementById(mount.inputid);
    if (!container || !input) {
        return;
    }
    mount.mounted = true;

    const mountWith = (value) => {
        require(['mod_vimipad/editor_lazy'], (editor) => {
            editor.mountValue(container, {
                value: value || '',
                onChange: () => {
                    // Read-only: nothing is written back.
                },
                profile: mount.profile,
                readonly: true,
                formconfig: mount.formconfig,
                initialView: 'canvas',
                showViewToggle: showtabs === true,
                getString: getString,
            });
        });
    };

    // Maps other than the first are not in the page: fetch this one before
    // mounting it, so the album stays light no matter how many maps it holds.
    if (mount.itemid && !input.value) {
        container.classList.add('vimigallery-loading');
        Ajax.call([{
            methodname: 'mod_vimigallery_get_item',
            args: {cmid: cmid, itemid: mount.itemid},
        }])[0].then((item) => {
            input.value = item.mapjson;
            container.classList.remove('vimigallery-loading');
            mountWith(item.mapjson);
            return item;
        }).catch((error) => {
            container.classList.remove('vimigallery-loading');
            // Allow a later swipe to try again rather than leaving a blank pane.
            mount.mounted = false;
            Notification.exception(error);
        });
        return;
    }

    mountWith(input.value);
};

/**
 * Initialise a gallery album.
 *
 * @param {string} rootId The id of the gallery root element.
 * @param {boolean} showtabs Whether the map/list toggle is enabled.
 * @param {number} cmid The gallery course module id (for comment posting).
 * @param {boolean} cancomment Whether the current user may post comments.
 * @return {void}
 */
export const init = (rootId, showtabs, cmid, cancomment) => {
    const root = document.getElementById(rootId);
    if (!root) {
        return;
    }

    // Form configs are shared per profile and delivered via a JSON script tag.
    let formconfigs = {};
    const configEl = document.getElementById(rootId + '_formconfigs');
    if (configEl) {
        try {
            formconfigs = JSON.parse(configEl.textContent) || {};
        } catch (e) {
            formconfigs = {};
        }
    }

    const slides = Array.prototype.slice.call(root.querySelectorAll('.vimigallery-slide'));
    const mounts = slides.map((slide) => {
        const container = slide.querySelector('.vimigallery-editor');
        if (!container) {
            return null;
        }
        const profile = container.getAttribute('data-profile') || 'conceptmap';
        return {
            containerid: container.id,
            inputid: container.getAttribute('data-input'),
            profile: profile,
            formconfig: formconfigs[profile],
            itemid: container.getAttribute('data-itemid'),
        };
    });
    const counter = root.querySelector('[data-vimigallery-current]');
    const total = mounts.length;
    let current = 0;
    if (total === 0) {
        return;
    }

    // Mount the given index and its immediate neighbours for smooth swiping.
    const ensureMounted = (index) => {
        [index - 1, index, index + 1].forEach((i) => {
            if (i >= 0 && i < total) {
                mountOne(mounts[i], showtabs, cmid);
            }
        });
    };

    const show = (index) => {
        if (index < 0 || index >= total || index === current) {
            return;
        }
        slides.forEach((slide, i) => {
            if (i === index) {
                slide.removeAttribute('hidden');
            } else {
                slide.setAttribute('hidden', 'hidden');
            }
        });
        current = index;
        if (counter) {
            counter.textContent = String(index + 1);
        }
        ensureMounted(index);
    };

    ensureMounted(0);

    const prev = root.querySelector('[data-vimigallery-prev]');
    const next = root.querySelector('[data-vimigallery-next]');
    if (prev) {
        prev.addEventListener('click', () => show(current - 1));
    }
    if (next) {
        next.addEventListener('click', () => show(current + 1));
    }

    // Keyboard navigation when the gallery is focused.
    root.setAttribute('tabindex', '0');
    root.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            show(current - 1);
        } else if (e.key === 'ArrowRight') {
            show(current + 1);
        }
    });

    // Touch swipe on the track.
    const track = root.querySelector('.vimigallery-track');
    if (track) {
        let startx = null;
        track.addEventListener('touchstart', (e) => {
            startx = e.changedTouches[0].clientX;
        }, {passive: true});
        track.addEventListener('touchend', (e) => {
            if (startx === null) {
                return;
            }
            const dx = e.changedTouches[0].clientX - startx;
            if (dx > 40) {
                show(current - 1);
            } else if (dx < -40) {
                show(current + 1);
            }
            startx = null;
        }, {passive: true});
    }

    // Comment posting: submit via web service and append the new comment.
    if (cancomment && cmid) {
        root.querySelectorAll('[data-comment-submit]').forEach((button) => {
            button.addEventListener('click', () => {
                const itemid = parseInt(button.getAttribute('data-comment-submit'), 10);
                const input = root.querySelector('[data-comment-input="' + itemid + '"]');
                const list = root.querySelector('[data-comments-for="' + itemid + '"]');
                if (!input || !list) {
                    return;
                }
                const content = input.value.trim();
                if (content === '') {
                    return;
                }
                button.disabled = true;
                Ajax.call([{
                    methodname: 'mod_vimigallery_post_comment',
                    args: {cmid: cmid, itemid: itemid, content: content},
                }])[0].then((comment) => {
                    list.appendChild(buildCommentElement(document, comment));
                    input.value = '';
                    button.disabled = false;
                    return comment;
                }).catch((error) => {
                    button.disabled = false;
                    Notification.exception(error);
                });
            });
        });
    }
};
