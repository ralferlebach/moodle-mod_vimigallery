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
 * Japanese language strings for mod_vimigallery.
 *
 * Machine-drafted starter translation for functional and RTL/CJK testing.
 * Not a certified translation; refine via AMOS. English is the source of truth.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcomment'] = 'コメントを追加';
$string['allowcomments'] = 'マップへのコメントを許可';
$string['allowcomments_help'] = '有効にすると、コメント権限を持つ受講者がギャラリー内の各マップにコメントできます。';
$string['arrange'] = 'マップを整理';
$string['arrange_help'] = 'マップをハンドルでドラッグして並べ替えるか、上下の矢印を使います。受講者に見せたくないマップは非表示にします。';
$string['author'] = '作成者';
$string['backtogallery'] = 'ギャラリーに戻る';
$string['choosemap'] = 'マップを選択...';
$string['comments'] = 'コメント';
$string['commentsdisabled'] = 'このギャラリーではコメントが有効になっていません。';
$string['compare'] = '比較';
$string['compareleft'] = '左';
$string['compareright'] = '右';
$string['completioncomments'] = '受講者はコメントを投稿する必要があります:';
$string['completioncommentsgroup'] = 'コメントを必須にする';
$string['completiondetail:comments'] = '少なくとも {$a} 件のコメントを投稿する';
$string['coupledscroll'] = '両方のマップを一緒にスクロール';
$string['datafieldsource'] = 'データベースフィールド';
$string['datafieldsource_help'] = 'このコースの「データベース」活動の ViMi Pad フィールドを選択します。その項目が、そのデータベースのアクセス規則に従ってこのギャラリーのマップになります。';
$string['displayheader'] = '表示';
$string['displaymode'] = '表示';
$string['displaymode_course'] = 'コースページ上（埋め込み、リンクなし）';
$string['displaymode_help'] = 'コースページ上ではラベルのようにセクションに直接ギャラリーを埋め込みます。別ページではページリソースのようにリンクと説明を表示します。';
$string['displaymode_page'] = '別ページ（リンクと説明）';
$string['emptycomment'] = 'コメントが空です。';
$string['enablecompare'] = '横並び比較を有効にする';
$string['enablecompare_help'] = '有効にすると、「比較」ビューで 2 つのマップを横並びに配置し、どの程度似ているかを確認できます。';
$string['freshness'] = '鮮度';
$string['freshness_help'] = 'ライブはギャラリーを表示するたびに現在の項目を読み取ります。静的とスナップショットは活動を保存したときに現在のマップのコピーを保存します。後で整理タブから更新できます。';
$string['freshness_live'] = 'ライブ（常に最新）';
$string['freshness_snapshot'] = 'スナップショット（保存されたコピー）';
$string['freshness_static'] = '静的（保存されたコピー）';
$string['invaliditem'] = 'そのマップはこのギャラリーに属していません。';
$string['map'] = 'マップ';
$string['mapn'] = 'マップ {$a}';
$string['modulename'] = 'ViMi ギャラリー';
$string['modulename_help'] = 'ViMi ギャラリーは 1 つ以上の ViMi Pad マップを読み取り専用で表示します。受講者はスクロール、ズーム、全画面表示ができます。';
$string['modulenameplural'] = 'ViMi ギャラリー';
$string['nodatafields'] = 'このコースには ViMi Pad データベースフィールドがありません';
$string['nomaps'] = 'このギャラリーにはまだマップがありません。';
$string['noquizzes'] = 'このコースには小テストがありません';
$string['novimipads'] = 'このコースには ViMi Pad 活動がありません';
$string['order'] = '順序';
$string['pluginadministration'] = 'ViMi ギャラリー管理';
$string['pluginname'] = 'ViMi ギャラリー';
$string['privacy:metadata'] = 'ユーザーがマップに書いたコメントは保存されます。ギャラリーが別の活動からマップを実体化する場合、保存されたコピーには受講者の作品や氏名が含まれることがあります。';
$string['privacy:metadata:vimigallery_comment'] = 'ユーザーがギャラリー内のマップに投稿するコメント。';
$string['privacy:metadata:vimigallery_comment:content'] = 'コメントの本文。';
$string['privacy:metadata:vimigallery_comment:timecreated'] = 'コメントが投稿された日時。';
$string['privacy:metadata:vimigallery_comment:userid'] = 'コメントを投稿したユーザー。';
$string['privacy:metadata:vimigallery_item'] = '別の活動から実体化されたマップのコピーで、受講者の作品を含むことがあります。';
$string['privacy:metadata:vimigallery_item:authorname'] = 'マップの作成者として表示される名前。';
$string['privacy:metadata:vimigallery_item:mapjson'] = '保存されたマップのコピー。';
$string['privacy:metadata:vimigallery_item:sourceuserid'] = 'コピーの元となった作品の受講者。';
$string['privacy:metadata:vimigallery_item_user'] = '実体化されたグループマップに貢献した受講者。';
$string['privacy:metadata:vimigallery_item_user:userid'] = 'マップに貢献した受講者。';
$string['profile'] = 'プロファイル';
$string['qtypesource'] = '小テスト';
$string['qtypesource_help'] = 'このコースの「小テスト」活動を選択します。その ViMi Pad 問題の模範解答がこのギャラリーのマップになります。模範解答は、小テストを採点できるユーザーにのみライブで表示されます。';
$string['refreshsnapshot'] = 'ソースから更新';
$string['showauthors'] = '作成者名を表示';
$string['showauthors_help'] = '有効にすると、各マップとともに保存された作成者名がその上に表示されます。';
$string['showtabs'] = 'マップ/リストタブを表示';
$string['showtabs_help'] = '有効にすると、閲覧者は各マップをグラフィック表示とリスト表示で切り替えられます。';
$string['similarity'] = '類似度: {$a}%';
$string['source_datafield'] = '「データベース」活動のフィールド';
$string['source_qtype'] = '「小テスト」活動（ViMi Pad 問題）';
$string['source_upload'] = 'アップロードしたファイル';
$string['source_vimipad'] = 'ViMi Pad 活動';
$string['sourcefiles'] = 'マップファイル（JSON）';
$string['sourcefiles_help'] = 'エクスポートした 1 つ以上の ViMi Pad マップを JSON ファイルとしてアップロードします。各ファイルがギャラリー内の 1 つのマップになります。';
$string['sourceheader'] = 'マップ';
$string['sourcemode'] = '表示';
$string['sourcemode_help'] = '参照は小テストの ViMi Pad 問題の模範解答を表示します。';
$string['sourcemode_reference'] = '模範解答';
$string['sourcemode_submissions'] = '受講者の提出物';
$string['sourcetype'] = 'マップソース';
$string['sourcetype_help'] = 'アップロードしたファイル：エクスポートした 1 つ以上の JSON マップ。「データベース」活動のフィールド：「データベース」活動の項目全体に保存された ViMi Pad の値。';
$string['vimigallery:addinstance'] = '新しい ViMi ギャラリーを追加';
$string['vimigallery:manageitems'] = 'ViMi ギャラリーのマップをキュレーション';
$string['vimigallery:view'] = 'ViMi ギャラリーを表示';
$string['vimipadsource'] = 'ViMi Pad 活動';
$string['vimipadsource_help'] = 'このコースの ViMi Pad 活動を選択します。提出モードでは、活動のアクセス規則に従って提出されたマップがギャラリーになります。参照モードでは、その模範解答が採点者に表示されます。';
$string['visible'] = '表示';
