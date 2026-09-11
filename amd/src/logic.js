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
 * Dependency-free helpers for the gallery viewer and comparison, kept separate
 * so they can be unit-tested without a browser or Moodle AMD dependencies.
 *
 * @module     mod_vimigallery/logic
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Couple two elements' scrolling so they mirror each other, using a re-entrancy
 * guard so mirroring one pane does not trigger an endless feedback loop.
 *
 * @param {HTMLElement} a The first scroll container.
 * @param {HTMLElement} b The second scroll container.
 * @return {void}
 */
export const coupleScroll = (a, b) => {
    const state = {syncing: false};
    const link = (from, to) => {
        from.addEventListener('scroll', () => {
            if (state.syncing) {
                state.syncing = false;
                return;
            }
            state.syncing = true;
            to.scrollTop = from.scrollTop;
            to.scrollLeft = from.scrollLeft;
        });
    };
    link(a, b);
    link(b, a);
};

/**
 * Build a comment list item from a comment object. Uses textContent throughout,
 * so author names and comment bodies cannot inject markup.
 *
 * @param {Document} doc The owning document.
 * @param {object} comment The comment ({authorname, content}).
 * @return {HTMLElement} The list item element.
 */
export const buildCommentElement = (doc, comment) => {
    const li = doc.createElement('li');
    li.className = 'vimigallery-comment';
    const meta = doc.createElement('span');
    meta.className = 'vimigallery-comment-meta text-muted small';
    meta.textContent = comment.authorname || '';
    const body = doc.createElement('div');
    body.className = 'vimigallery-comment-body';
    body.textContent = comment.content || '';
    li.appendChild(meta);
    li.appendChild(body);
    return li;
};
