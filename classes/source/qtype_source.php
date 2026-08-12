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
 * Submissions mode exposes learners' attempt responses instead of the reference
 * map, subject to the viewer's own quiz permissions and group membership.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_source implements source_interface {
    /** Reference (model solution) mode. */
    public const MODE_REFERENCE = 'reference';

    /** Learner submissions mode. */
    public const MODE_SUBMISSIONS = 'submissions';

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

        // The qtype_vimipad plugin is an optional peer: this source reads its options table
        // directly, so if the plugin was never installed (or has been removed)
        // the gallery must degrade to empty rather than fail on a missing table.
        if (!\core_component::get_plugin_directory('qtype', 'vimipad')) {
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

        if ($this->mode === self::MODE_SUBMISSIONS) {
            return $this->submission_items($cm, $context, $userid);
        }

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
            $item->sourceuserid = isset($entry->sourceuserid) ? (int) $entry->sourceuserid : null;
            $items[] = $item;
        }

        return $items;
    }

    /**
     * The submitted vimipad answers of a quiz, as the viewer may see them.
     *
     * A learner sees only their own finished attempts; a viewer with report or
     * grade access sees everyone's, restricted to their own groups under separate
     * groups.
     *
     * @param \stdClass $cm The quiz course module.
     * @param \context_module $context The quiz context.
     * @param int $userid The viewer.
     * @return \stdClass[] The visible submitted maps.
     */
    protected function submission_items($cm, $context, int $userid): array {
        global $DB;

        $slots = $this->quiz_vimipad_slots($cm->instance);
        if (empty($slots)) {
            return [];
        }

        $canviewall = has_capability('mod/quiz:viewreports', $context, $userid)
            || has_capability('mod/quiz:grade', $context, $userid);

        $params = ['quizid' => $cm->instance, 'state' => 'finished'];
        $where = ['quiz = :quizid', 'state = :state', 'preview = 0'];

        if (!$canviewall) {
            $where[] = 'userid = :owner';
            $params['owner'] = $userid;
        } else if (
            groups_get_activity_groupmode($cm) == SEPARATEGROUPS
                && !has_capability('moodle/site:accessallgroups', $context, $userid)
        ) {
            $allowed = $this->group_peer_userids($cm->course, $userid);
            if (empty($allowed)) {
                return [];
            }
            [$insql, $inparams] = $DB->get_in_or_equal($allowed, SQL_PARAMS_NAMED, 'u');
            $where[] = "userid $insql";
            $params += $inparams;
        }

        // Newest first, bounded: each attempt costs a question-usage load, which
        // cannot be batched, so an unbounded cohort would make this page
        // unusable. The slice is reversed afterwards to restore chronology.
        $attempts = $DB->get_records_select(
            'quiz_attempts',
            implode(' AND ', $where),
            $params,
            'timefinish DESC, id DESC',
            'id, userid, uniqueid',
            0,
            self::MAX_ITEMS
        );
        $attempts = array_reverse($attempts, true);

        // Author names in one query rather than one per attempt.
        $usercache = \mod_vimigallery\local\comment_service::author_names(
            array_map(fn($a) => (int) $a->userid, $attempts)
        );

        $entries = [];
        foreach ($attempts as $attempt) {
            try {
                $quba = \question_engine::load_questions_usage_by_activity($attempt->uniqueid);
            } catch (\Exception $e) {
                continue;
            }
            foreach ($slots as $slot => $questionid) {
                $qa = $quba->get_question_attempt($slot);
                if (!$qa) {
                    continue;
                }
                $mapjson = $qa->get_last_qt_var('answer');
                if ($mapjson === null || trim((string) $mapjson) === '') {
                    continue;
                }
                $entries[] = (object) [
                    'mapjson' => $mapjson,
                    'authorname' => $usercache[(int) $attempt->userid] ?? '',
                    'sourceuserid' => (int) $attempt->userid,
                ];
            }
        }

        return $this->normalise_entries($entries);
    }

    /**
     * The user ids sharing at least one group with the viewer.
     *
     * @param int $courseid The course id.
     * @param int $userid The viewer.
     * @return int[] The peer user ids (including the viewer).
     */
    protected function group_peer_userids(int $courseid, int $userid): array {
        $groups = groups_get_user_groups($courseid, $userid)[0] ?? [];
        $ids = [$userid => $userid];
        foreach ($groups as $groupid) {
            foreach (groups_get_members($groupid, 'u.id') as $member) {
                $ids[$member->id] = (int) $member->id;
            }
        }
        return array_values($ids);
    }

    /**
     * The vimipad slots of a quiz mapped to their question ids.
     *
     * @param int $quizid The quiz instance id.
     * @return array<int,int> Map of slot number => question id.
     */
    protected function quiz_vimipad_slots(int $quizid): array {
        global $DB;
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
        $slots = [];
        foreach ($DB->get_records_sql($sql, ['quizid' => $quizid]) as $row) {
            $slots[(int) $row->slot] = (int) $row->questionid;
        }
        return $slots;
    }

    /**
     * Normalise and validate raw {mapjson, authorname} entries into items.
     *
     * @param \stdClass[] $entries The raw entries.
     * @return \stdClass[] The valid items in order.
     */
    protected function normalise_entries(array $entries): array {
        $items = [];
        $sortorder = 0;
        foreach ($entries as $entry) {
            $decoded = json_decode($entry->mapjson, true);
            if (!is_array($decoded) || !isset($decoded['nodes'])) {
                continue;
            }
            $profile = isset($decoded['profile']) && is_string($decoded['profile'])
                ? $decoded['profile'] : 'conceptmap';
            $item = new \stdClass();
            $item->id = 'qs' . $sortorder;
            $item->mapjson = $entry->mapjson;
            $item->profile = \core_text::substr($profile, 0, 40);
            $item->authorname = \core_text::substr($entry->authorname, 0, 255);
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
