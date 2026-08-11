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
        ]);

        return html_writer::div(
            $configscript . $track . $controls,
            'vimigallery',
            ['id' => $rootid, 'data-vimigallery' => '1']
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
