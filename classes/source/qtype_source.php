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

namespace mod_vimigallery\source;

/**
 * Maps from the ViMi Pad questions of a Quiz (mod_quiz) activity.
 *
 * In reference mode the gallery shows the model solutions of the quiz's ViMi Pad
 * questions. Because a model solution reveals the answer, reference mode is only
 * available to viewers who may grade the quiz; a teacher may still publish it to
 * everyone by choosing a static or snapshot freshness, which materialises the
 * maps at save time.
 *
 * Submissions mode (learners' attempt responses) is handled separately and is
 * not provided by this class yet.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_source implements source_interface {
    /** Reference (model solution) mode. */
    public const MODE_REFERENCE = 'reference';

    /** @var int The course module id of the source quiz. */
    protected int $cmid;

    /** @var string The source mode (currently only reference). */
    protected string $mode;

    /**
     * Constructor.
     *
     * @param int $cmid The course module id of the source quiz.
     * @param string $mode One of the MODE_* constants.
     */
    public function __construct(int $cmid, string $mode = self::MODE_REFERENCE) {
        $this->cmid = $cmid;
        $this->mode = $mode;
    }

    /**
     * The visible maps of the source quiz for the viewer.
     *
     * @param int|null $userid The viewer, or null for the current user.
     * @return \stdClass[] The visible maps, in question order.
     */
    public function get_items(?int $userid = null): array {
        global $DB, $USER;

        $userid = $userid ?? (int) $USER->id;

        if ($this->mode !== self::MODE_REFERENCE) {
            return [];
        }

        $cm = get_coursemodule_from_id('quiz', $this->cmid, 0, false, IGNORE_MISSING);
        if (!$cm) {
            return [];
        }
        $modinfo = get_fast_modinfo($cm->course, $userid);
        if (!isset($modinfo->cms[$cm->id]) || !$modinfo->cms[$cm->id]->uservisible) {
            return [];
        }
        $context = \context_module::instance($cm->id);
        // A model solution reveals the answer, so only graders may see it live.
        if (!has_capability('mod/quiz:grade', $context, $userid)) {
            return [];
        }

        $questionids = $this->quiz_vimipad_questionids($cm->instance);
        if (empty($questionids)) {
            return [];
        }

        [$insql, $params] = $DB->get_in_or_equal($questionids, SQL_PARAMS_NAMED, 'q');
        $references = $DB->get_records_select(
            'qtype_vimipad_options',
            "questionid $insql",
            $params,
            '',
            'questionid, referencemap'
        );

        $items = [];
        $sortorder = 0;
        foreach ($questionids as $questionid) {
            if (!isset($references[$questionid])) {
                continue;
            }
            $mapjson = $references[$questionid]->referencemap;
            if ($mapjson === null || trim((string) $mapjson) === '') {
                continue;
            }
            $decoded = json_decode($mapjson, true);
            if (!is_array($decoded) || !isset($decoded['nodes'])) {
                continue;
            }
            $profile = isset($decoded['profile']) && is_string($decoded['profile'])
                ? $decoded['profile'] : 'conceptmap';

            $item = new \stdClass();
            $item->id = 'qt' . $questionid;
            $item->mapjson = $mapjson;
            $item->profile = \core_text::substr($profile, 0, 40);
            $item->authorname = '';
            $item->sortorder = $sortorder++;
            $item->visible = 1;
            $items[] = $item;
        }

        return $items;
    }

    /**
     * The ids of the ViMi Pad questions used in a quiz, in slot order.
     *
     * @param int $quizid The quiz instance id.
     * @return int[] The question ids of type vimipad.
     */
    protected function quiz_vimipad_questionids(int $quizid): array {
        global $DB;

        // Resolve each quiz slot to its current question version, keeping only
        // ViMi Pad questions, ordered by slot.
        $sql = "SELECT qs.slot, q.id AS questionid
                  FROM {quiz_slots} qs
                  JOIN {question_references} qr
                        ON qr.itemid = qs.id
                       AND qr.component = 'mod_quiz'
                       AND qr.questionarea = 'slot'
                  JOIN {question_bank_entries} qbe ON qbe.id = qr.questionbankentryid
                  JOIN {question_versions} qv ON qv.questionbankentryid = qbe.id
                  JOIN {question} q ON q.id = qv.questionid
                 WHERE qs.quizid = :quizid
                       AND q.qtype = 'vimipad'
                       AND qv.version = (
                            SELECT MAX(v.version)
                              FROM {question_versions} v
                             WHERE v.questionbankentryid = qbe.id
                       )
              ORDER BY qs.slot ASC";
        $rows = $DB->get_records_sql($sql, ['quizid' => $quizid]);

        $ids = [];
        foreach ($rows as $row) {
            $ids[] = (int) $row->questionid;
        }
        return $ids;
    }
}
