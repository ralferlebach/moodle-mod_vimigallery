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

/**
 * English strings for mod_vimigallery.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcomment'] = 'Add comment';
$string['allowcomments'] = 'Allow comments on maps';
$string['allowcomments_help'] = 'When enabled, learners with the comment capability can comment on each map in the gallery.';
$string['arrange'] = 'Arrange maps';
$string['arrange_help'] = 'Drag a map by its handle to reorder, or use the up/down arrows. Hide maps you do not want learners to see.';
$string['author'] = 'Author';
$string['backtogallery'] = 'Back to the gallery';
$string['choosemap'] = 'Choose a map...';
$string['comments'] = 'Comments';
$string['commentsdisabled'] = 'Comments are not enabled for this gallery.';
$string['compare'] = 'Compare';
$string['compareleft'] = 'Left';
$string['compareright'] = 'Right';
$string['completioncomments'] = 'Learner must post comments:';
$string['completioncommentsgroup'] = 'Require comments';
$string['completiondetail:comments'] = 'Post at least {$a} comment(s)';
$string['coupledscroll'] = 'Scroll both maps together';
$string['datafieldsource'] = 'Database field';
$string['datafieldsource_help'] = 'Choose a ViMi Pad field of a Database activity in this course. Its entries become the maps in this gallery, following the access rules of that database.';
$string['displayheader'] = 'Display';
$string['displaymode'] = 'Display';
$string['displaymode_course'] = 'On the course page (embedded, no link)';
$string['displaymode_help'] = 'On the course page embeds the gallery directly in the section, like a label. On a separate page shows a link and description, like a page resource.';
$string['displaymode_page'] = 'On a separate page (link and description)';
$string['emptycomment'] = 'The comment is empty.';
$string['enablecompare'] = 'Enable side-by-side comparison';
$string['enablecompare_help'] = 'When enabled, a "Compare" view lets users place two maps side by side and see how similar they are.';
$string['freshness'] = 'Freshness';
$string['freshness_help'] = 'Live reads the current entries every time the gallery is viewed. Static and snapshot store a copy of the current maps when you save the activity; refresh them later from the arrange tab.';
$string['freshness_live'] = 'Live (always current)';
$string['freshness_snapshot'] = 'Snapshot (stored copy)';
$string['freshness_static'] = 'Static (stored copy)';
$string['invaliditem'] = 'That map does not belong to this gallery.';
$string['map'] = 'Map';
$string['mapn'] = 'Map {$a}';
$string['modulename'] = 'ViMi Gallery';
$string['modulename_help'] = 'The ViMi Gallery shows one or more ViMi Pad maps read-only. Learners can scroll, zoom and view full screen.';
$string['modulenameplural'] = 'ViMi Galleries';
$string['nodatafields'] = 'No ViMi Pad database fields in this course';
$string['nomaps'] = 'This gallery has no maps yet.';
$string['noquizzes'] = 'No quizzes in this course';
$string['novimipads'] = 'No ViMi Pad activities in this course';
$string['novimipads'] = 'No ViMi Pad activities in this course';
$string['order'] = 'Order';
$string['pluginadministration'] = 'ViMi Gallery administration';
$string['pluginname'] = 'ViMi Gallery';
$string['privacy:metadata'] = 'The ViMi Gallery plugin does not store any personal data; it displays maps supplied by teachers.';
$string['privacy:metadata:vimigallery_comment'] = 'Comments a user posts on maps in a gallery.';
$string['privacy:metadata:vimigallery_comment:content'] = 'The text of the comment.';
$string['privacy:metadata:vimigallery_comment:timecreated'] = 'When the comment was posted.';
$string['privacy:metadata:vimigallery_comment:userid'] = 'The user who posted the comment.';
$string['profile'] = 'Profile';
$string['qtypesource'] = 'Quiz';
$string['qtypesource_help'] = 'Choose a Quiz activity in this course. The model solutions of its ViMi Pad questions become the maps in this gallery. Model solutions are only shown live to users who may grade the quiz.';
$string['refreshsnapshot'] = 'Refresh from source';
$string['showauthors'] = 'Show author names';
$string['showauthors_help'] = 'When enabled, the author name stored with each map is shown above it.';
$string['showtabs'] = 'Show map/list tabs';
$string['showtabs_help'] = 'When enabled, viewers can switch each map between the graphical and the list view.';
$string['similarity'] = 'Similarity: {$a}%';
$string['source_datafield'] = 'A Database activity field';
$string['source_qtype'] = 'A Quiz activity (ViMi Pad questions)';
$string['source_upload'] = 'Uploaded files';
$string['source_vimipad'] = 'A ViMi Pad activity';
$string['source_vimipad'] = 'A ViMi Pad activity';
$string['sourcefiles'] = 'Map files (JSON)';
$string['sourcefiles_help'] = 'Upload one or more exported ViMi Pad maps as JSON files. Each file becomes one map in the gallery.';
$string['sourceheader'] = 'Maps';
$string['sourcemode'] = 'Show';
$string['sourcemode_help'] = 'Reference shows the model solutions of the quiz ViMi Pad questions.';
$string['sourcemode_reference'] = 'Model solutions';
$string['sourcemode_submissions'] = 'Learner submissions';
$string['sourcemode_submissions'] = 'Learner submissions';
$string['sourcetype'] = 'Map source';
$string['sourcetype_help'] = 'Uploaded files: one or more exported JSON maps. A Database activity field: the ViMi Pad values stored across the entries of a Database activity.';
$string['vimigallery:addinstance'] = 'Add a new ViMi Gallery';
$string['vimigallery:manageitems'] = 'Curate the maps in a ViMi Gallery';
$string['vimigallery:view'] = 'View a ViMi Gallery';
$string['vimipadsource'] = 'ViMi Pad activity';
$string['vimipadsource'] = 'ViMi Pad activity';
$string['vimipadsource_help'] = 'Choose a ViMi Pad activity in this course. In submissions mode its submitted maps become the gallery, following the activity access rules; in reference mode its model solution is shown to graders.';
$string['vimipadsource_help'] = 'Choose a ViMi Pad activity in this course. Show either its model solution or the submitted maps. Submissions follow the activity access rules: a learner sees only their own and their groups; a grader sees all.';
$string['visible'] = 'Visible';
