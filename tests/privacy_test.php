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

use mod_vimigallery\local\comment_service;
use mod_vimigallery\privacy\provider;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\writer;

/**
 * Tests for the mod_vimigallery privacy provider.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_vimigallery\privacy\provider
 */
final class privacy_test extends \core_privacy\tests\provider_testcase {
    /**
     * Build a gallery with one commentable item.
     *
     * @return array [context, cm (stdClass), itemid]
     */
    private function make(): array {
        global $DB;
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module(
            'vimigallery',
            ['course' => $course->id, 'allowcomments' => 1]
        );
        $cm = get_coursemodule_from_instance('vimigallery', $module->id);
        $itemid = (int) $DB->insert_record('vimigallery_item', (object) [
            'galleryid' => $module->id,
            'sortorder' => 0,
            'visible' => 1,
            'sourcetype' => 'upload',
            'profile' => 'conceptmap',
            'mapjson' => '{"nodes":[]}',
            'authorname' => '',
            'contenthash' => sha1('x'),
            'timecreated' => time(),
        ]);
        return [\context_module::instance($cm->id), $cm, $itemid];
    }

    /**
     * A user's comments are exported for the gallery context.
     *
     * @return void
     */
    public function test_export(): void {
        $this->resetAfterTest();
        [$context, $cm, $itemid] = $this->make();
        $user = $this->getDataGenerator()->create_user();
        comment_service::post($cm, $itemid, (int) $user->id, 'Exported comment');

        $this->export_context_data_for_user((int) $user->id, $context, 'mod_vimigallery');
        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
    }

    /**
     * Deleting for a user removes only their comments.
     *
     * @return void
     */
    public function test_delete_for_user(): void {
        global $DB;
        $this->resetAfterTest();
        [$context, $cm, $itemid] = $this->make();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        comment_service::post($cm, $itemid, (int) $user1->id, 'u1');
        comment_service::post($cm, $itemid, (int) $user2->id, 'u2');

        $contextlist = new approved_contextlist($user1, 'mod_vimigallery', [$context->id]);
        provider::delete_data_for_user($contextlist);

        $this->assertEquals(0, $DB->count_records('vimigallery_comment', ['userid' => $user1->id]));
        $this->assertEquals(1, $DB->count_records('vimigallery_comment', ['userid' => $user2->id]));
    }

    /**
     * Deleting for all users in a context clears every comment.
     *
     * @return void
     */
    public function test_delete_all(): void {
        global $DB;
        $this->resetAfterTest();
        [$context, $cm, $itemid] = $this->make();
        $user = $this->getDataGenerator()->create_user();
        comment_service::post($cm, $itemid, (int) $user->id, 'gone');

        provider::delete_data_for_all_users_in_context($context);
        $this->assertEquals(0, $DB->count_records('vimigallery_comment', ['galleryid' => $cm->instance]));
    }

    /**
     * A materialised copy of a learner's map is discoverable, exportable and
     * deleted on request. The copy is derived data, so a deletion request
     * removes it rather than anonymising it; the source activity remains the
     * authoritative record.
     *
     * @return void
     */
    public function test_materialised_item_provenance(): void {
        global $DB;
        $this->resetAfterTest();

        [$context, $cm] = $this->make();
        $learner = $this->getDataGenerator()->create_user();
        $mapjson = '{"profile":"conceptmap","nodes":[],"relations":[]}';
        $DB->insert_record('vimigallery_item', (object) [
            'galleryid' => $cm->instance,
            'sortorder' => 0,
            'visible' => 1,
            'sourcetype' => 'vimipad',
            'profile' => 'conceptmap',
            'mapjson' => $mapjson,
            'authorname' => 'Learner One',
            'contenthash' => sha1($mapjson),
            'sourceuserid' => $learner->id,
            'timecreated' => time(),
        ]);

        // Discovery finds the gallery for this learner even without comments.
        $contexts = array_map('intval', provider::get_contexts_for_userid((int) $learner->id)->get_contextids());
        $this->assertContains((int) $context->id, $contexts);

        // The learner is listed among the users with data in the context.
        $userlist = new \core_privacy\local\request\userlist($context, 'mod_vimigallery');
        provider::get_users_in_context($userlist);
        $this->assertContains((int) $learner->id, array_map('intval', $userlist->get_userids()));

        // The copy is exported.
        $this->export_context_data_for_user((int) $learner->id, $context, 'mod_vimigallery');
        $this->assertTrue(writer::with_context($context)->has_any_data());

        // And removed on a deletion request.
        $contextlist = new approved_contextlist($learner, 'mod_vimigallery', [$context->id]);
        provider::delete_data_for_user($contextlist);
        $this->assertEquals(0, $DB->count_records('vimigallery_item', ['sourceuserid' => $learner->id]));
    }

    /**
     * Teacher-uploaded items are not touched by a learner's deletion request.
     *
     * @return void
     */
    public function test_upload_items_are_not_personal_data(): void {
        global $DB;
        $this->resetAfterTest();

        [$context, $cm] = $this->make();
        $learner = $this->getDataGenerator()->create_user();
        $mapjson = '{"profile":"conceptmap","nodes":[],"relations":[]}';
        $DB->insert_record('vimigallery_item', (object) [
            'galleryid' => $cm->instance,
            'sortorder' => 0,
            'visible' => 1,
            'sourcetype' => 'upload',
            'profile' => 'conceptmap',
            'mapjson' => $mapjson,
            'authorname' => '',
            'contenthash' => sha1($mapjson),
            'sourceuserid' => null,
            'timecreated' => time(),
        ]);

        $before = $DB->count_records('vimigallery_item', ['galleryid' => $cm->instance]);
        $contextlist = new approved_contextlist($learner, 'mod_vimigallery', [$context->id]);
        provider::delete_data_for_user($contextlist);

        // Nothing was removed: none of these items derive from a learner.
        $this->assertEquals($before, $DB->count_records('vimigallery_item', ['galleryid' => $cm->instance]));
    }

    /**
     * A contributor to a materialised group map is discoverable and exportable,
     * and a deletion request removes only their link: the map and its group
     * label stay, because they also hold other people's work.
     *
     * @return void
     */
    public function test_group_map_contribution_is_anonymised_not_deleted(): void {
        global $DB;
        $this->resetAfterTest();

        [$context, $cm] = $this->make();
        $one = $this->getDataGenerator()->create_user();
        $two = $this->getDataGenerator()->create_user();

        $mapjson = '{"profile":"conceptmap","nodes":[],"relations":[]}';
        $itemid = (int) $DB->insert_record('vimigallery_item', (object) [
            'galleryid' => $cm->instance,
            'sortorder' => 0,
            'visible' => 1,
            'sourcetype' => 'vimipad',
            'profile' => 'conceptmap',
            'mapjson' => $mapjson,
            'authorname' => 'Group A',
            'contenthash' => sha1($mapjson),
            // A group map has no single owner.
            'sourceuserid' => null,
            'sourcekey' => 'vp1',
            'timecreated' => time(),
        ]);
        foreach ([$one, $two] as $member) {
            $DB->insert_record('vimigallery_item_user', (object) [
                'itemid' => $itemid,
                'userid' => $member->id,
            ]);
        }

        // Discovery finds the gallery for a contributor, even though the item
        // carries no sourceuserid.
        $contexts = array_map('intval', provider::get_contexts_for_userid((int) $one->id)->get_contextids());
        $this->assertContains((int) $context->id, $contexts);

        // Both contributors are listed among the users with data here.
        $userlist = new \core_privacy\local\request\userlist($context, 'mod_vimigallery');
        provider::get_users_in_context($userlist);
        $listed = array_map('intval', $userlist->get_userids());
        $this->assertContains((int) $one->id, $listed);
        $this->assertContains((int) $two->id, $listed);

        // The shared map is exported for a contributor.
        $this->export_context_data_for_user((int) $one->id, $context, 'mod_vimigallery');
        $this->assertTrue(writer::with_context($context)->has_any_data());

        // A deletion request removes only that person's link.
        $contextlist = new approved_contextlist($one, 'mod_vimigallery', [$context->id]);
        provider::delete_data_for_user($contextlist);

        $item = $DB->get_record('vimigallery_item', ['id' => $itemid]);
        $this->assertNotEmpty($item, 'the shared map must survive');
        $this->assertSame('Group A', $item->authorname, 'the group label is not personal data');
        $this->assertEquals(0, $DB->count_records('vimigallery_item_user', [
            'itemid' => $itemid,
            'userid' => $one->id,
        ]));
        $this->assertEquals(1, $DB->count_records('vimigallery_item_user', [
            'itemid' => $itemid,
            'userid' => $two->id,
        ]), 'the other contributor keeps their link');
    }

    /**
     * Insert a materialised item.
     *
     * @param object $cm The gallery course module.
     * @param int|null $sourceuserid The single owner, or null for a group map.
     * @param array $contributors Contributor user ids (for a group map).
     * @param string $key A unique source key.
     * @return int The new item id.
     */
    private function add_item($cm, ?int $sourceuserid, array $contributors, string $key): int {
        global $DB;
        $map = '{"profile":"conceptmap","nodes":[],"relations":[]}';
        $itemid = (int) $DB->insert_record('vimigallery_item', (object) [
            'galleryid' => $cm->instance,
            'sortorder' => 0,
            'visible' => 1,
            'sourcetype' => $sourceuserid !== null ? 'vimipad' : ($contributors ? 'vimipad' : 'upload'),
            'profile' => 'conceptmap',
            'mapjson' => $map,
            'authorname' => $contributors ? 'Group A' : '',
            'contenthash' => sha1($key),
            'sourceuserid' => $sourceuserid,
            'sourcekey' => $key,
            'timecreated' => time(),
        ]);
        foreach ($contributors as $userid) {
            $DB->insert_record('vimigallery_item_user', (object) ['itemid' => $itemid, 'userid' => $userid]);
        }
        return $itemid;
    }

    /**
     * A context-wide deletion removes every learner-derived item - individual
     * and group maps alike - and their contributor links and comments, while a
     * teacher upload survives. A null sourceuserid must not be read as proof that
     * a group map is not personal data.
     *
     * @return void
     */
    public function test_context_delete_removes_group_maps(): void {
        global $DB;
        $this->resetAfterTest();

        [$context, $cm] = $this->make();
        $a = $this->getDataGenerator()->create_user();
        $b = $this->getDataGenerator()->create_user();

        // make() already left one upload item (item 1). Add the other two.
        $upload = (int) $DB->get_field_sql(
            "SELECT id FROM {vimigallery_item} WHERE galleryid = ? ORDER BY id ASC",
            [$cm->instance]
        );
        $individual = $this->add_item($cm, (int) $a->id, [], 'vp-individual');
        $group = $this->add_item($cm, null, [(int) $a->id, (int) $b->id], 'vp-group');

        \mod_vimigallery\local\comment_service::post($cm, $individual, (int) $a->id, 'on 2');
        \mod_vimigallery\local\comment_service::post($cm, $group, (int) $b->id, 'on 3');

        provider::delete_data_for_all_users_in_context($context);

        // Teacher upload stays; both learner maps go.
        $this->assertTrue($DB->record_exists('vimigallery_item', ['id' => $upload]));
        $this->assertFalse($DB->record_exists('vimigallery_item', ['id' => $individual]));
        $this->assertFalse($DB->record_exists('vimigallery_item', ['id' => $group]));
        // No orphaned contributor links or comments.
        $this->assertEquals(0, $DB->count_records('vimigallery_item_user'));
        $this->assertEquals(0, $DB->count_records('vimigallery_comment', ['galleryid' => $cm->instance]));
    }

    /**
     * Deleting a single contributor still leaves a group map and its other
     * contributor untouched, so context-wide and single-user deletion keep their
     * different, correct semantics.
     *
     * @return void
     */
    public function test_single_contributor_delete_keeps_shared_map(): void {
        global $DB;
        $this->resetAfterTest();

        [$context, $cm] = $this->make();
        $a = $this->getDataGenerator()->create_user();
        $b = $this->getDataGenerator()->create_user();
        $group = $this->add_item($cm, null, [(int) $a->id, (int) $b->id], 'vp-group');

        $contextlist = new approved_contextlist($a, 'mod_vimigallery', [$context->id]);
        provider::delete_data_for_user($contextlist);

        $this->assertTrue($DB->record_exists('vimigallery_item', ['id' => $group]), 'the shared map survives');
        $this->assertEquals(0, $DB->count_records('vimigallery_item_user', ['itemid' => $group, 'userid' => $a->id]));
        $this->assertEquals(1, $DB->count_records('vimigallery_item_user', ['itemid' => $group, 'userid' => $b->id]));
    }
}
