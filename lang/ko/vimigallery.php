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
 * Korean language strings for mod_vimigallery.
 *
 * Machine-drafted starter translation for functional and RTL/CJK testing.
 * Not a certified translation; refine via AMOS. English is the source of truth.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcomment'] = '댓글 추가';
$string['allowcomments'] = '맵에 댓글 허용';
$string['allowcomments_help'] = '활성화하면 댓글 권한이 있는 학습자가 갤러리의 각 맵에 댓글을 달 수 있습니다.';
$string['arrange'] = '맵 정렬';
$string['arrange_help'] = '핸들을 잡아 맵을 끌어 순서를 바꾸거나 위／아래 화살표를 사용하세요. 학습자에게 보이고 싶지 않은 맵은 숨기세요.';
$string['author'] = '작성자';
$string['backtogallery'] = '갤러리로 돌아가기';
$string['choosemap'] = '맵 선택...';
$string['comments'] = '댓글';
$string['commentsdisabled'] = '이 갤러리에서는 댓글이 활성화되어 있지 않습니다.';
$string['compare'] = '비교';
$string['compareleft'] = '왼쪽';
$string['compareright'] = '오른쪽';
$string['completioncomments'] = '학습자가 댓글을 작성해야 합니다:';
$string['completioncommentsgroup'] = '댓글 요구';
$string['completiondetail:comments'] = '댓글을 {$a}개 이상 작성';
$string['coupledscroll'] = '두 맵을 함께 스크롤';
$string['datafieldsource'] = '데이터베이스 필드';
$string['datafieldsource_help'] = '이 강좌의 「데이터베이스」 활동에 있는 ViMi Pad 필드를 선택하세요. 해당 항목이 그 데이터베이스의 접근 규칙에 따라 이 갤러리의 맵이 됩니다.';
$string['displayheader'] = '표시';
$string['displaymode'] = '표시';
$string['displaymode_course'] = '강좌 페이지에 표시(내장, 링크 없음)';
$string['displaymode_help'] = '강좌 페이지에 표시하면 레이블처럼 섹션에 갤러리를 직접 내장합니다. 별도 페이지에 표시하면 페이지 자원처럼 링크와 설명을 보여줍니다.';
$string['displaymode_page'] = '별도 페이지에 표시(링크와 설명)';
$string['emptycomment'] = '댓글이 비어 있습니다.';
$string['enablecompare'] = '나란히 비교 활성화';
$string['enablecompare_help'] = '활성화하면 「비교」 보기에서 두 맵을 나란히 놓고 얼마나 비슷한지 확인할 수 있습니다.';
$string['freshness'] = '최신성';
$string['freshness_help'] = '실시간은 갤러리를 볼 때마다 현재 항목을 읽습니다. 정적과 스냅샷은 활동을 저장할 때 현재 맵의 사본을 저장하며, 이후 정렬 탭에서 새로 고칠 수 있습니다.';
$string['freshness_live'] = '실시간(항상 최신)';
$string['freshness_snapshot'] = '스냅샷(저장된 사본)';
$string['freshness_static'] = '정적(저장된 사본)';
$string['invaliditem'] = '해당 맵은 이 갤러리에 속하지 않습니다.';
$string['map'] = '맵';
$string['mapn'] = '맵 {$a}';
$string['modulename'] = 'ViMi 갤러리';
$string['modulename_help'] = 'ViMi 갤러리는 하나 이상의 ViMi Pad 맵을 읽기 전용으로 표시합니다. 학습자는 스크롤, 확대／축소, 전체 화면 보기를 할 수 있습니다.';
$string['modulenameplural'] = 'ViMi 갤러리';
$string['nodatafields'] = '이 강좌에는 ViMi Pad 데이터베이스 필드가 없습니다';
$string['nomaps'] = '이 갤러리에는 아직 맵이 없습니다.';
$string['noquizzes'] = '이 강좌에는 퀴즈가 없습니다';
$string['novimipads'] = '이 강좌에는 ViMi Pad 활동이 없습니다';
$string['order'] = '순서';
$string['pluginadministration'] = 'ViMi 갤러리 관리';
$string['pluginname'] = 'ViMi 갤러리';
$string['privacy:metadata'] = '사용자가 맵에 작성한 댓글이 저장됩니다. 갤러리가 다른 활동에서 맵을 실체화할 경우, 저장된 사본에 학습자의 작업과 이름이 포함될 수 있습니다.';
$string['privacy:metadata:vimigallery_comment'] = '사용자가 갤러리의 맵에 작성한 댓글.';
$string['privacy:metadata:vimigallery_comment:content'] = '댓글 본문.';
$string['privacy:metadata:vimigallery_comment:timecreated'] = '댓글이 작성된 시각.';
$string['privacy:metadata:vimigallery_comment:userid'] = '댓글을 작성한 사용자.';
$string['privacy:metadata:vimigallery_item'] = '다른 활동에서 실체화된 맵의 사본으로, 학습자의 작업이 포함될 수 있습니다.';
$string['privacy:metadata:vimigallery_item:authorname'] = '맵 작성자로 표시되는 이름.';
$string['privacy:metadata:vimigallery_item:mapjson'] = '저장된 맵 사본.';
$string['privacy:metadata:vimigallery_item:sourceuserid'] = '사본이 유래한 작업의 학습자.';
$string['privacy:metadata:vimigallery_item_user'] = '실체화된 그룹 맵에 기여한 학습자.';
$string['privacy:metadata:vimigallery_item_user:userid'] = '맵에 기여한 학습자.';
$string['profile'] = '프로필';
$string['qtypesource'] = '퀴즈';
$string['qtypesource_help'] = '이 강좌의 「퀴즈」 활동을 선택하세요. 그 ViMi Pad 문제의 모범 답안이 이 갤러리의 맵이 됩니다. 모범 답안은 퀴즈를 채점할 수 있는 사용자에게만 실시간으로 표시됩니다.';
$string['refreshsnapshot'] = '원본에서 새로 고침';
$string['showauthors'] = '작성자 이름 표시';
$string['showauthors_help'] = '활성화하면 각 맵과 함께 저장된 작성자 이름이 그 위에 표시됩니다.';
$string['showtabs'] = '맵／목록 탭 표시';
$string['showtabs_help'] = '활성화하면 보는 사람이 각 맵을 그래픽 보기와 목록 보기 사이에서 전환할 수 있습니다.';
$string['similarity'] = '유사도: {$a}%';
$string['source_datafield'] = '「데이터베이스」 활동의 필드';
$string['source_qtype'] = '「퀴즈」 활동(ViMi Pad 문제)';
$string['source_upload'] = '업로드한 파일';
$string['source_vimipad'] = 'ViMi Pad 활동';
$string['sourcefiles'] = '맵 파일(JSON)';
$string['sourcefiles_help'] = '내보낸 ViMi Pad 맵을 하나 이상 JSON 파일로 업로드하세요. 각 파일이 갤러리의 맵 하나가 됩니다.';
$string['sourceheader'] = '맵';
$string['sourcemode'] = '표시';
$string['sourcemode_help'] = '참조는 퀴즈의 ViMi Pad 문제에 대한 모범 답안을 표시합니다.';
$string['sourcemode_reference'] = '모범 답안';
$string['sourcemode_submissions'] = '학습자 제출물';
$string['sourcetype'] = '맵 원본';
$string['sourcetype_help'] = '업로드한 파일: 내보낸 JSON 맵 하나 이상. 「데이터베이스」 활동의 필드: 「데이터베이스」 활동의 항목 전반에 저장된 ViMi Pad 값.';
$string['vimigallery:addinstance'] = '새 ViMi 갤러리 추가';
$string['vimigallery:manageitems'] = 'ViMi 갤러리의 맵 큐레이션';
$string['vimigallery:view'] = 'ViMi 갤러리 보기';
$string['vimipadsource'] = 'ViMi Pad 활동';
$string['vimipadsource_help'] = '이 강좌의 ViMi Pad 활동을 선택하세요. 제출 모드에서는 활동의 접근 규칙에 따라 제출된 맵이 갤러리가 되고, 참조 모드에서는 모범 답안이 채점자에게 표시됩니다.';
$string['visible'] = '표시됨';
