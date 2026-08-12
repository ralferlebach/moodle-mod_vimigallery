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

use html_writer;
use plugin_renderer_base;

/**
 * Renderer for mod_vimigallery.
 *
 * Renders each map as a read-only embedded ViMi Pad editor (mod_vimipad
 * mountValue), mounted lazily. The maps are frozen values, so no network,
 * submission or export is involved.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {
    /**
     * Render a gallery.
     *
     * @param gallery $gallery The gallery to render.
     * @return string HTML.
     */
    protected function render_gallery(gallery $gallery): string {
        $items = $gallery->get_items();
        if (empty($items)) {
            return html_writer::div(
                get_string('nomaps', 'mod_vimigallery'),
                'vimigallery-empty text-muted'
            );
        }

        $this->preload_editor_strings();

        $rootid = 'vimigallery_' . $gallery->instance->id;
        $multiple = count($items) > 1;

        $formconfigs = [];
        $slides = [];
        $index = 0;
        foreach ($items as $item) {
            $baseid = $rootid . '_i' . $item->id;
            $inputid = $baseid . '_value';
            $containerid = $baseid . '_editor';

            // The form config only depends on the profile, so collect it once per
            // profile and pass the map via a JSON script element (not js_call_amd,
            // which caps argument length).
            if (!isset($formconfigs[$item->profile])) {
                $formconfigs[$item->profile] = \mod_vimipad\profile\profiles::form_config($item->profile);
            }

            $hidden = html_writer::empty_tag('input', [
                'type' => 'hidden',
                'id' => $inputid,
                'value' => (string) $item->mapjson,
            ]);
            // Dynamic height: grows with the viewport but never below the floor.
            $container = html_writer::tag('div', '', [
                'id' => $containerid,
                'class' => 'vimigallery-editor',
                'style' => 'min-height:480px;height:60vh;',
                'data-input' => $inputid,
                'data-profile' => $item->profile,
            ]);

            $slidebody = '';
            if ($gallery->show_authors() && $item->authorname !== '') {
                $slidebody .= html_writer::div(
                    s($item->authorname),
                    'vimigallery-author font-weight-bold mb-1'
                );
            }
            $slidebody .= $hidden . $container;

            // Comments belong to materialised items (numeric ids); live sources
            // have synthetic ids and are not commentable.
            if ($gallery->allow_comments() && is_numeric($item->id)) {
                $slidebody .= $this->render_comments($gallery, (int) $item->id);
            }

            // Only the first slide is visible initially; the rest are hidden and
            // mounted lazily as the learner swipes to them.
            $slideattrs = ['class' => 'vimigallery-slide', 'data-index' => $index];
            if ($index !== 0) {
                $slideattrs['hidden'] = 'hidden';
            }
            $slides[] = html_writer::tag('div', $slidebody, $slideattrs);
            $index++;
        }

        $configscript = html_writer::tag(
            'script',
            json_encode($formconfigs, JSON_HEX_TAG | JSON_HEX_AMP),
            ['type' => 'application/json', 'id' => $rootid . '_formconfigs']
        );

        $track = html_writer::div(implode('', $slides), 'vimigallery-track');

        $controls = '';
        if ($multiple) {
            $prev = html_writer::tag('button', '&#8249;', [
                'type' => 'button',
                'class' => 'btn btn-outline-secondary vimigallery-prev',
                'data-vimigallery-prev' => '1',
                'aria-label' => get_string('previous'),
            ]);
            $next = html_writer::tag('button', '&#8250;', [
                'type' => 'button',
                'class' => 'btn btn-outline-secondary vimigallery-next',
                'data-vimigallery-next' => '1',
                'aria-label' => get_string('next'),
            ]);
            $counter = html_writer::div(
                html_writer::span('1', '', ['data-vimigallery-current' => '1'])
                    . ' / ' . count($items),
                'vimigallery-counter mx-2'
            );
            $controls = html_writer::div(
                $prev . $counter . $next,
                'vimigallery-controls d-flex align-items-center justify-content-center mt-2'
            );
        }

        $this->page->requires->js_call_amd('mod_vimigallery/viewer', 'init', [
            $rootid,
            (bool) $gallery->show_tabs(),
            $gallery->cmid(),
            (bool) $gallery->can_comment(),
        ]);

        return html_writer::div(
            $configscript . $track . $controls,
            'vimigallery',
            ['id' => $rootid, 'data-vimigallery' => '1']
        );
    }

    /**
     * Render the comment list and (if allowed) the post form for one map.
     *
     * @param gallery $gallery The gallery renderable.
     * @param int $itemid The materialised item id.
     * @return string The comments HTML.
     */
    protected function render_comments(gallery $gallery, int $itemid): string {
        $comments = \mod_vimigallery\local\comment_service::get_for_item($itemid);

        $list = '';
        foreach ($comments as $comment) {
            $meta = html_writer::tag(
                'span',
                s($comment->authorname) . ' · ' . userdate($comment->timecreated),
                ['class' => 'vimigallery-comment-meta text-muted small']
            );
            $body = html_writer::tag('div', nl2br(s($comment->content)), ['class' => 'vimigallery-comment-body']);
            $list .= html_writer::tag('li', $meta . $body, ['class' => 'vimigallery-comment']);
        }
        $listhtml = html_writer::tag('ul', $list, [
            'class' => 'vimigallery-comments list-unstyled',
            'data-comments-for' => $itemid,
        ]);

        $form = '';
        if ($gallery->can_comment()) {
            $textarea = html_writer::tag('textarea', '', [
                'class' => 'form-control vimigallery-comment-input',
                'rows' => 2,
                'data-comment-input' => $itemid,
                'aria-label' => get_string('addcomment', 'mod_vimigallery'),
            ]);
            $button = html_writer::tag('button', get_string('addcomment', 'mod_vimigallery'), [
                'type' => 'button',
                'class' => 'btn btn-secondary btn-sm mt-1 vimigallery-comment-submit',
                'data-comment-submit' => $itemid,
            ]);
            $form = html_writer::div($textarea . $button, 'vimigallery-comment-form mt-2');
        }

        return html_writer::div(
            html_writer::tag('h5', get_string('comments', 'mod_vimigallery'), ['class' => 'mt-3 h6'])
            . $listhtml . $form,
            'vimigallery-comment-section'
        );
    }

    /**
     * Render the side-by-side comparison view: two selectors, two read-only maps
     * and an optional similarity score.
     *
     * @param compare $compare The comparison renderable.
     * @return string The comparison HTML.
     */
    protected function render_compare(compare $compare): string {
        $items = $compare->get_items();
        $rootid = 'vimigallerycmp_' . $compare->instance->id;

        // Build the two selectors as a plain GET form.
        $options = ['' => get_string('choosemap', 'mod_vimigallery')];
        $index = 1;
        foreach ($items as $item) {
            $label = ($item->authorname !== '')
                ? $item->authorname
                : get_string('mapn', 'mod_vimigallery', $index);
            $options[(string) $item->id] = $label;
            $index++;
        }
        $leftid = $compare->get_left() ? (string) $compare->get_left()->id : '';
        $rightid = $compare->get_right() ? (string) $compare->get_right()->id : '';

        $url = new \moodle_url('/mod/vimigallery/compare.php', ['id' => $compare->cmid()]);
        $selectors = html_writer::start_tag('form', [
            'method' => 'get', 'action' => $url->out_omit_querystring(), 'class' => 'form-inline mb-3',
        ]);
        $selectors .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $compare->cmid()]);
        $selectors .= html_writer::label(get_string('compareleft', 'mod_vimigallery'), 'cmpleft', true, ['class' => 'mr-1']);
        $selectors .= html_writer::select($options, 'left', $leftid, false, ['id' => 'cmpleft', 'class' => 'mr-3']);
        $selectors .= html_writer::label(get_string('compareright', 'mod_vimigallery'), 'cmpright', true, ['class' => 'mr-1']);
        $selectors .= html_writer::select($options, 'right', $rightid, false, ['id' => 'cmpright', 'class' => 'mr-3']);
        $selectors .= html_writer::tag(
            'button',
            get_string('compare', 'mod_vimigallery'),
            ['type' => 'submit', 'class' => 'btn btn-primary btn-sm']
        );
        $selectors .= html_writer::end_tag('form');

        // Nothing selected yet: just the selectors.
        if ($compare->get_left() === null || $compare->get_right() === null) {
            return html_writer::div($selectors, 'vimigallery-compare', ['id' => $rootid]);
        }

        $this->preload_editor_strings();

        // Similarity line.
        $similarity = $compare->similarity();
        $simhtml = '';
        if ($similarity !== null) {
            $pct = round($similarity * 100);
            $simhtml = html_writer::div(
                get_string('similarity', 'mod_vimigallery', $pct),
                'vimigallery-similarity alert alert-info'
            );
        }

        // Coupled-scroll toggle.
        $toggle = html_writer::div(
            html_writer::checkbox(
                'coupledscroll',
                1,
                false,
                get_string('coupledscroll', 'mod_vimigallery'),
                ['data-compare-couple' => '1']
            ),
            'vimigallery-compare-toggle mb-2'
        );

        // Two read-only panes.
        $formconfigs = [];
        $panes = '';
        foreach (['left' => $compare->get_left(), 'right' => $compare->get_right()] as $side => $item) {
            if (!isset($formconfigs[$item->profile])) {
                $formconfigs[$item->profile] = \mod_vimipad\profile\profiles::form_config($item->profile);
            }
            $inputid = $rootid . '_' . $side . '_value';
            $containerid = $rootid . '_' . $side . '_editor';
            $header = html_writer::tag(
                'h5',
                ($item->authorname !== '' ? s($item->authorname) : get_string('map', 'mod_vimigallery')),
                ['class' => 'h6']
            );
            $hidden = html_writer::empty_tag('input', ['type' => 'hidden', 'id' => $inputid, 'value' => (string) $item->mapjson]);
            $container = html_writer::tag('div', '', [
                'id' => $containerid,
                'class' => 'vimigallery-editor vimigallery-compare-pane',
                'style' => 'min-height:420px;height:55vh;',
                'data-input' => $inputid,
                'data-profile' => $item->profile,
                'data-compare-side' => $side,
            ]);
            $panes .= html_writer::div($header . $hidden . $container, 'col-md-6');
        }
        $grid = html_writer::div($panes, 'row vimigallery-compare-grid');

        $configscript = html_writer::tag(
            'script',
            json_encode($formconfigs, JSON_HEX_TAG | JSON_HEX_AMP),
            ['type' => 'application/json', 'id' => $rootid . '_formconfigs']
        );

        $this->page->requires->js_call_amd('mod_vimigallery/compare', 'init', [$rootid]);

        return html_writer::div(
            $selectors . $simhtml . $toggle . $configscript . $grid,
            'vimigallery-compare',
            ['id' => $rootid, 'data-vimigallery-compare' => '1']
        );
    }

    /**
     * Preload the mod_vimipad editor language strings for the embedded editor.
     *
     * @return void
     */
    protected function preload_editor_strings(): void {
        $strings = get_string_manager()->load_component_strings('mod_vimipad', current_language());
        $keys = [];
        foreach (array_keys($strings) as $key) {
            if (strpos($key, 'editor:') === 0 || strpos($key, 'constraint:') === 0) {
                $keys[] = $key;
            }
        }
        if ($keys) {
            $this->page->requires->strings_for_js($keys, 'mod_vimipad');
        }
    }
}
