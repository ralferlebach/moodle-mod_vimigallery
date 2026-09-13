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
 * Chinese (Traditional) language strings for mod_vimigallery.
 *
 * Machine-drafted starter translation for functional and RTL/CJK testing.
 * Not a certified translation; refine via AMOS. English is the source of truth.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcomment'] = '新增評論';
$string['allowcomments'] = '允許對圖進行評論';
$string['allowcomments_help'] = '啟用後，具有評論權限的學員可以對畫廊中的每張圖進行評論。';
$string['arrange'] = '排列圖';
$string['arrange_help'] = '拖曳圖的控點以重新排序，或使用上／下箭頭。隱藏你不想讓學員看到的圖。';
$string['author'] = '作者';
$string['backtogallery'] = '返回畫廊';
$string['choosemap'] = '選擇一張圖...';
$string['comments'] = '評論';
$string['commentsdisabled'] = '此畫廊未啟用評論。';
$string['compare'] = '比較';
$string['compareleft'] = '左';
$string['compareright'] = '右';
$string['completioncomments'] = '學員必須發表評論：';
$string['completioncommentsgroup'] = '要求評論';
$string['completiondetail:comments'] = '至少發表 {$a} 則評論';
$string['coupledscroll'] = '同時捲動兩張圖';
$string['datafieldsource'] = '資料庫欄位';
$string['datafieldsource_help'] = '選擇本課程中某個「資料庫」活動的 ViMi Pad 欄位。其項目將依該資料庫的存取規則成為此畫廊中的圖。';
$string['displayheader'] = '顯示';
$string['displaymode'] = '顯示';
$string['displaymode_course'] = '在課程頁面上（內嵌，無連結）';
$string['displaymode_help'] = '在課程頁面上會像標籤一樣將畫廊直接內嵌到區段中。在獨立頁面上會像頁面資源一樣顯示連結與說明。';
$string['displaymode_page'] = '在獨立頁面上（連結與說明）';
$string['emptycomment'] = '評論是空的。';
$string['enablecompare'] = '啟用並排比較';
$string['enablecompare_help'] = '啟用後，「比較」檢視可讓使用者將兩張圖並排放置，查看它們有多相似。';
$string['freshness'] = '時效';
$string['freshness_help'] = '即時會在每次檢視畫廊時讀取目前的項目。靜態與快照會在你儲存活動時儲存目前圖的副本；之後可在排列分頁中重新整理。';
$string['freshness_live'] = '即時（永遠最新）';
$string['freshness_snapshot'] = '快照（已儲存副本）';
$string['freshness_static'] = '靜態（已儲存副本）';
$string['invaliditem'] = '該圖不屬於此畫廊。';
$string['map'] = '圖';
$string['mapn'] = '圖 {$a}';
$string['modulename'] = 'ViMi 畫廊';
$string['modulename_help'] = 'ViMi 畫廊以唯讀方式顯示一張或多張 ViMi Pad 圖。學員可以捲動、縮放並以全螢幕檢視。';
$string['modulenameplural'] = 'ViMi 畫廊';
$string['nodatafields'] = '本課程中沒有 ViMi Pad 資料庫欄位';
$string['nomaps'] = '此畫廊尚無任何圖。';
$string['noquizzes'] = '本課程中沒有測驗';
$string['novimipads'] = '本課程中沒有 ViMi Pad 活動';
$string['order'] = '順序';
$string['pluginadministration'] = 'ViMi 畫廊管理';
$string['pluginname'] = 'ViMi 畫廊';
$string['privacy:metadata'] = '使用者在圖上撰寫的評論會被儲存。當畫廊從其他活動實體化圖時，所儲存的副本也可能包含學員的作品及其姓名。';
$string['privacy:metadata:vimigallery_comment'] = '使用者在畫廊的圖上發表的評論。';
$string['privacy:metadata:vimigallery_comment:content'] = '評論的文字。';
$string['privacy:metadata:vimigallery_comment:timecreated'] = '評論的發表時間。';
$string['privacy:metadata:vimigallery_comment:userid'] = '發表評論的使用者。';
$string['privacy:metadata:vimigallery_item'] = '從其他活動實體化的圖的副本，可能包含學員的作品。';
$string['privacy:metadata:vimigallery_item:authorname'] = '為圖的作者顯示的名稱。';
$string['privacy:metadata:vimigallery_item:mapjson'] = '圖的已儲存副本。';
$string['privacy:metadata:vimigallery_item:sourceuserid'] = '副本所源自作品的學員。';
$string['privacy:metadata:vimigallery_item_user'] = '哪些學員對實體化的小組圖作出了貢獻。';
$string['privacy:metadata:vimigallery_item_user:userid'] = '對圖作出貢獻的學員。';
$string['profile'] = '設定檔';
$string['qtypesource'] = '測驗';
$string['qtypesource_help'] = '選擇本課程中的「測驗」活動。其 ViMi Pad 題目的參考答案將成為此畫廊中的圖。參考答案僅對可評閱該測驗的使用者即時顯示。';
$string['refreshsnapshot'] = '從來源重新整理';
$string['showauthors'] = '顯示作者姓名';
$string['showauthors_help'] = '啟用後，與每張圖一起儲存的作者姓名會顯示在其上方。';
$string['showtabs'] = '顯示圖／清單分頁';
$string['showtabs_help'] = '啟用後，檢視者可以在圖形檢視與清單檢視之間切換每張圖。';
$string['similarity'] = '相似度：{$a}%';
$string['source_datafield'] = '某個「資料庫」活動的欄位';
$string['source_qtype'] = '某個「測驗」活動（ViMi Pad 題目）';
$string['source_upload'] = '已上傳的檔案';
$string['source_vimipad'] = '某個 ViMi Pad 活動';
$string['sourcefiles'] = '圖檔（JSON）';
$string['sourcefiles_help'] = '上傳一個或多個匯出為 JSON 檔的 ViMi Pad 圖。每個檔案會成為畫廊中的一張圖。';
$string['sourceheader'] = '圖';
$string['sourcemode'] = '顯示';
$string['sourcemode_help'] = '參考會顯示測驗 ViMi Pad 題目的參考答案。';
$string['sourcemode_reference'] = '參考答案';
$string['sourcemode_submissions'] = '學員提交';
$string['sourcetype'] = '圖來源';
$string['sourcetype_help'] = '已上傳的檔案：一個或多個匯出的 JSON 圖。某個「資料庫」活動的欄位：跨「資料庫」活動各項目儲存的 ViMi Pad 值。';
$string['vimigallery:addinstance'] = '新增 ViMi 畫廊';
$string['vimigallery:manageitems'] = '策劃 ViMi 畫廊中的圖';
$string['vimigallery:view'] = '檢視 ViMi 畫廊';
$string['vimipadsource'] = 'ViMi Pad 活動';
$string['vimipadsource_help'] = '選擇本課程中的 ViMi Pad 活動。在提交模式下，其提交的圖將依活動的存取規則成為畫廊；在參考模式下，其參考答案會向評閱者顯示。';
$string['visible'] = '可見';
