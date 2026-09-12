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
 * Arabic language strings for mod_vimigallery.
 *
 * Machine-drafted starter translation for functional and RTL/CJK testing.
 * Not a certified translation; refine via AMOS. English is the source of truth.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcomment'] = 'إضافة تعليق';
$string['allowcomments'] = 'السماح بالتعليقات على الخرائط';
$string['allowcomments_help'] = 'عند التفعيل، يمكن للمتعلمين الذين يملكون صلاحية التعليق التعليق على كل خريطة في المعرض.';
$string['arrange'] = 'ترتيب الخرائط';
$string['arrange_help'] = 'اسحب الخريطة من المقبض لإعادة الترتيب، أو استخدم سهمي الأعلى/الأسفل. أخفِ الخرائط التي لا تريد إظهارها للمتعلمين.';
$string['author'] = 'المؤلف';
$string['backtogallery'] = 'العودة إلى المعرض';
$string['choosemap'] = 'اختر خريطة...';
$string['comments'] = 'التعليقات';
$string['commentsdisabled'] = 'التعليقات غير مفعّلة لهذا المعرض.';
$string['compare'] = 'مقارنة';
$string['compareleft'] = 'يسار';
$string['compareright'] = 'يمين';
$string['completioncomments'] = 'يجب على المتعلم نشر تعليقات:';
$string['completioncommentsgroup'] = 'اشتراط التعليقات';
$string['completiondetail:comments'] = 'انشر {$a} تعليق على الأقل';
$string['coupledscroll'] = 'تمرير الخريطتين معاً';
$string['datafieldsource'] = 'حقل قاعدة بيانات';
$string['datafieldsource_help'] = 'اختر حقل ViMi Pad من نشاط «قاعدة بيانات» في هذه المادة. تصبح مُدخلاته خرائط هذا المعرض، وفق قواعد الوصول لتلك القاعدة.';
$string['displayheader'] = 'العرض';
$string['displaymode'] = 'العرض';
$string['displaymode_course'] = 'في صفحة المادة (مضمّن، بدون رابط)';
$string['displaymode_help'] = 'في صفحة المادة يضمّن المعرض مباشرةً في القسم، مثل التسمية. في صفحة منفصلة يعرض رابطاً ووصفاً، مثل مورد الصفحة.';
$string['displaymode_page'] = 'في صفحة منفصلة (رابط ووصف)';
$string['emptycomment'] = 'التعليق فارغ.';
$string['enablecompare'] = 'تفعيل المقارنة جنباً إلى جنب';
$string['enablecompare_help'] = 'عند التفعيل، تتيح طريقة عرض «مقارنة» للمستخدمين وضع خريطتين جنباً إلى جنب ورؤية مدى تشابههما.';
$string['freshness'] = 'الحداثة';
$string['freshness_help'] = 'يقرأ الوضع «مباشر» المُدخلات الحالية في كل مرة يُعرَض فيها المعرض. يحفظ «ثابت» و«لقطة» نسخة من الخرائط الحالية عند حفظ النشاط؛ حدّثها لاحقاً من تبويب الترتيب.';
$string['freshness_live'] = 'مباشر (محدّث دائماً)';
$string['freshness_snapshot'] = 'لقطة (نسخة محفوظة)';
$string['freshness_static'] = 'ثابت (نسخة محفوظة)';
$string['invaliditem'] = 'هذه الخريطة لا تنتمي إلى هذا المعرض.';
$string['map'] = 'خريطة';
$string['mapn'] = 'خريطة {$a}';
$string['modulename'] = 'معرض ViMi';
$string['modulename_help'] = 'يعرض معرض ViMi خريطة ViMi Pad واحدة أو أكثر للقراءة فقط. يمكن للمتعلمين التمرير والتكبير والعرض بملء الشاشة.';
$string['modulenameplural'] = 'معارض ViMi';
$string['nodatafields'] = 'لا توجد حقول قاعدة بيانات ViMi Pad في هذه المادة';
$string['nomaps'] = 'لا يحتوي هذا المعرض على خرائط بعد.';
$string['noquizzes'] = 'لا توجد اختبارات في هذه المادة';
$string['novimipads'] = 'لا توجد أنشطة ViMi Pad في هذه المادة';
$string['order'] = 'الترتيب';
$string['pluginadministration'] = 'إدارة معرض ViMi';
$string['pluginname'] = 'معرض ViMi';
$string['privacy:metadata'] = 'تُحفَظ التعليقات التي يكتبها المستخدمون على الخرائط. عندما يجسّد المعرض خرائط من نشاط آخر، قد تحتوي النسخ المحفوظة أيضاً على أعمال المتعلمين وأسمائهم.';
$string['privacy:metadata:vimigallery_comment'] = 'التعليقات التي ينشرها المستخدم على الخرائط في معرض.';
$string['privacy:metadata:vimigallery_comment:content'] = 'نص التعليق.';
$string['privacy:metadata:vimigallery_comment:timecreated'] = 'وقت نشر التعليق.';
$string['privacy:metadata:vimigallery_comment:userid'] = 'المستخدم الذي نشر التعليق.';
$string['privacy:metadata:vimigallery_item'] = 'نسخ من خرائط مُجسَّدة من نشاط آخر، وقد تحتوي على أعمال المتعلمين.';
$string['privacy:metadata:vimigallery_item:authorname'] = 'الاسم المعروض لمؤلف الخريطة.';
$string['privacy:metadata:vimigallery_item:mapjson'] = 'النسخة المحفوظة من الخريطة.';
$string['privacy:metadata:vimigallery_item:sourceuserid'] = 'المتعلم الذي اشتُقّت النسخة من عمله.';
$string['privacy:metadata:vimigallery_item_user'] = 'المتعلمون الذين أسهموا في خريطة مجموعة مُجسَّدة.';
$string['privacy:metadata:vimigallery_item_user:userid'] = 'متعلم أسهم في الخريطة.';
$string['profile'] = 'الملف التعريفي';
$string['qtypesource'] = 'اختبار';
$string['qtypesource_help'] = 'اختر نشاط «اختبار» في هذه المادة. تصبح الحلول النموذجية لأسئلة ViMi Pad فيه خرائط هذا المعرض. تُعرَض الحلول النموذجية مباشرةً فقط للمستخدمين الذين يمكنهم تقدير الاختبار.';
$string['refreshsnapshot'] = 'تحديث من المصدر';
$string['showauthors'] = 'إظهار أسماء المؤلفين';
$string['showauthors_help'] = 'عند التفعيل، يُعرَض اسم المؤلف المحفوظ مع كل خريطة أعلاها.';
$string['showtabs'] = 'إظهار تبويبَي الخريطة/القائمة';
$string['showtabs_help'] = 'عند التفعيل، يمكن للمشاهدين تبديل كل خريطة بين العرض الرسومي وعرض القائمة.';
$string['similarity'] = 'التشابه: {$a}%';
$string['source_datafield'] = 'حقل نشاط «قاعدة بيانات»';
$string['source_qtype'] = 'نشاط «اختبار» (أسئلة ViMi Pad)';
$string['source_upload'] = 'ملفات مرفوعة';
$string['source_vimipad'] = 'نشاط ViMi Pad';
$string['sourcefiles'] = 'ملفات الخرائط (JSON)';
$string['sourcefiles_help'] = 'ارفع خريطة ViMi Pad مُصدَّرة واحدة أو أكثر كملفات JSON. يصبح كل ملف خريطة واحدة في المعرض.';
$string['sourceheader'] = 'الخرائط';
$string['sourcemode'] = 'إظهار';
$string['sourcemode_help'] = 'يعرض «المرجع» الحلول النموذجية لأسئلة ViMi Pad في الاختبار.';
$string['sourcemode_reference'] = 'الحلول النموذجية';
$string['sourcemode_submissions'] = 'تسليمات المتعلمين';
$string['sourcetype'] = 'مصدر الخرائط';
$string['sourcetype_help'] = 'ملفات مرفوعة: خريطة JSON مُصدَّرة واحدة أو أكثر. حقل نشاط «قاعدة بيانات»: قيم ViMi Pad المحفوظة عبر مُدخلات نشاط «قاعدة بيانات».';
$string['vimigallery:addinstance'] = 'إضافة معرض ViMi جديد';
$string['vimigallery:manageitems'] = 'تنظيم خرائط معرض ViMi';
$string['vimigallery:view'] = 'عرض معرض ViMi';
$string['vimipadsource'] = 'نشاط ViMi Pad';
$string['vimipadsource_help'] = 'اختر نشاط ViMi Pad في هذه المادة. في وضع التسليمات تصبح خرائطه المُسلَّمة هي المعرض، وفق قواعد وصول النشاط؛ وفي وضع المرجع يُعرَض حله النموذجي للمقدِّرين.';
$string['visible'] = 'مرئي';
