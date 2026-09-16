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

namespace mod_vimigallery;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/vimigallery/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Tests for the callbacks Moodle core invokes on this module.
 *
 * These exist because of a whole class of bug the earlier tests could not see:
 * a callback that core runs while rendering one of ITS pages. Such a failure is
 * never visible from a test that calls the plugin directly, but it takes the
 * core page down with it - vimigallery_cm_info_view() in particular renders the
 * gallery into the course page, so an exception there breaks the course page for
 * every participant, not just this activity.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     ::vimigallery_cm_info_view
 */
final class course_callbacks_test extends \advanced_testcase {
    /**
     * Create a gallery with items.
     *
     * @param string $displaymode Either 'page' or 'course'.
     * @param int $items How many maps to put in the album.
     * @return array An array of the course and the module record.
     */
    private function make_gallery(string $displaymode, int $items = 2): array {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('vimigallery', [
            'course' => $course->id,
            'displaymode' => $displaymode,
            'showauthors' => 1,
        ]);

        $map = '{"profile":"conceptmap","nodes":[{"stableid":"a","label":"Root"}],"relations":[]}';
        for ($i = 0; $i < $items; $i++) {
            $DB->insert_record('vimigallery_item', (object) [
                'galleryid' => $module->id,
                'sortorder' => $i,
                'visible' => 1,
                'sourcetype' => 'upload',
                'profile' => 'conceptmap',
                'mapjson' => $map,
                'authorname' => 'Author ' . $i,
                'timecreated' => time(),
            ]);
        }

        return [$course, $module];
    }

    /**
     * In course display mode the album is rendered into the course page.
     *
     * @return void
     */
    public function test_cm_info_view_embeds_album_in_course_page(): void {
        global $PAGE;
        $this->resetAfterTest();
        $this->setAdminUser();

        [$course, $module] = $this->make_gallery('course');
        $PAGE->set_url('/course/view.php', ['id' => $course->id]);

        $cm = get_fast_modinfo($course)->get_cm($module->cmid);
        vimigallery_cm_info_view($cm);

        $content = $cm->content;
        $this->assertNotEmpty($content, 'The album must be embedded in course display mode.');
        $this->assertStringContainsString('vimigallery-slide', $content);
        $this->assertFalse($cm->has_view(), 'An embedded gallery must not offer a separate view link.');
    }

    /**
     * In page display mode nothing is embedded and the link is kept.
     *
     * @return void
     */
    public function test_cm_info_view_leaves_page_mode_alone(): void {
        global $PAGE;
        $this->resetAfterTest();
        $this->setAdminUser();

        [$course, $module] = $this->make_gallery('page');
        $PAGE->set_url('/course/view.php', ['id' => $course->id]);

        $cm = get_fast_modinfo($course)->get_cm($module->cmid);
        vimigallery_cm_info_view($cm);

        $this->assertEmpty($cm->content);
        $this->assertTrue($cm->has_view(), 'A page-mode gallery keeps its activity link.');
    }

    /**
     * An empty gallery must not break the course page.
     *
     * A gallery is empty right after a teacher creates it, so this is the state
     * the course page sees most often.
     *
     * @return void
     */
    public function test_cm_info_view_survives_empty_gallery(): void {
        global $PAGE;
        $this->resetAfterTest();
        $this->setAdminUser();

        [$course, $module] = $this->make_gallery('course', 0);
        $PAGE->set_url('/course/view.php', ['id' => $course->id]);

        $cm = get_fast_modinfo($course)->get_cm($module->cmid);
        vimigallery_cm_info_view($cm);

        // No exception is the point; whatever it renders must be a string.
        $this->assertIsString((string) $cm->content);
    }

    /**
     * Rendering the course page itself works with an embedded gallery present.
     *
     * This goes one level further than the callback: it drives core's own course
     * renderer, which is what actually breaks for every participant when the
     * callback misbehaves.
     *
     * @return void
     */
    public function test_course_page_renders_with_embedded_gallery(): void {
        global $PAGE;
        $this->resetAfterTest();
        $this->setAdminUser();

        [$course] = $this->make_gallery('course');
        $PAGE->set_url('/course/view.php', ['id' => $course->id]);
        $PAGE->set_context(\context_course::instance($course->id));

        $modinfo = get_fast_modinfo($course);
        $rendered = '';
        foreach ($modinfo->get_cms() as $cm) {
            if ($cm->modname === 'vimigallery') {
                $rendered .= (string) $cm->content;
            }
        }

        $this->assertStringContainsString('vimigallery-slide', $rendered);
    }

    /**
     * The module declares the feature set core asks about.
     *
     * @return void
     */
    public function test_supports_answers_core_feature_queries(): void {
        $this->resetAfterTest();

        // Core calls plugin_supports() on every module for these; an unknown
        // feature must return null rather than raise a notice.
        $this->assertIsBool((bool) vimigallery_supports(FEATURE_MOD_INTRO));
        $this->assertNull(vimigallery_supports('some_feature_that_does_not_exist'));
    }
}
