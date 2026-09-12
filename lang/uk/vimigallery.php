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
 * Ukrainian language strings for mod_vimigallery.
 *
 * Machine-drafted starter translation for functional and RTL/CJK testing.
 * Not a certified translation; refine via AMOS. English is the source of truth.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcomment'] = 'Додати коментар';
$string['allowcomments'] = 'Дозволити коментарі до карт';
$string['allowcomments_help'] = 'Коли ввімкнено, студенти з правом коментування можуть коментувати кожну карту в галереї.';
$string['arrange'] = 'Упорядкувати карти';
$string['arrange_help'] = 'Перетягніть карту за маркер, щоб змінити порядок, або скористайтеся стрілками вгору/вниз. Приховайте карти, які не хочете показувати студентам.';
$string['author'] = 'Автор';
$string['backtogallery'] = 'Назад до галереї';
$string['choosemap'] = 'Виберіть карту...';
$string['comments'] = 'Коментарі';
$string['commentsdisabled'] = 'Коментарі не ввімкнено для цієї галереї.';
$string['compare'] = 'Порівняти';
$string['compareleft'] = 'Ліворуч';
$string['compareright'] = 'Праворуч';
$string['completioncomments'] = 'Студент має залишити коментарі:';
$string['completioncommentsgroup'] = 'Вимагати коментарі';
$string['completiondetail:comments'] = 'Залишіть щонайменше {$a} коментар(ів)';
$string['coupledscroll'] = 'Прокручувати обидві карти разом';
$string['datafieldsource'] = 'Поле бази даних';
$string['datafieldsource_help'] = 'Виберіть поле ViMi Pad діяльності «База даних» у цьому курсі. Його записи стануть картами цієї галереї відповідно до правил доступу тієї бази даних.';
$string['displayheader'] = 'Відображення';
$string['displaymode'] = 'Відображення';
$string['displaymode_course'] = 'На сторінці курсу (вбудовано, без посилання)';
$string['displaymode_help'] = 'На сторінці курсу вбудовує галерею безпосередньо в секцію, як напис. На окремій сторінці показує посилання й опис, як ресурс-сторінку.';
$string['displaymode_page'] = 'На окремій сторінці (посилання та опис)';
$string['emptycomment'] = 'Коментар порожній.';
$string['enablecompare'] = 'Увімкнути порівняння поруч';
$string['enablecompare_help'] = 'Коли ввімкнено, вигляд «Порівняти» дає змогу розмістити дві карти поруч і побачити, наскільки вони схожі.';
$string['freshness'] = 'Актуальність';
$string['freshness_help'] = 'Наживо читає поточні записи щоразу під час перегляду галереї. Статичний і знімок зберігають копію поточних карт під час збереження діяльності; оновіть їх пізніше на вкладці впорядкування.';
$string['freshness_live'] = 'Наживо (завжди актуально)';
$string['freshness_snapshot'] = 'Знімок (збережена копія)';
$string['freshness_static'] = 'Статичний (збережена копія)';
$string['invaliditem'] = 'Ця карта не належить до цієї галереї.';
$string['map'] = 'Карта';
$string['mapn'] = 'Карта {$a}';
$string['modulename'] = 'Галерея ViMi';
$string['modulename_help'] = 'Галерея ViMi показує одну або кілька карт ViMi Pad лише для читання. Студенти можуть прокручувати, масштабувати та переглядати на весь екран.';
$string['modulenameplural'] = 'Галереї ViMi';
$string['nodatafields'] = 'У цьому курсі немає полів бази даних ViMi Pad';
$string['nomaps'] = 'У цій галереї ще немає карт.';
$string['noquizzes'] = 'У цьому курсі немає тестів';
$string['novimipads'] = 'У цьому курсі немає діяльностей ViMi Pad';
$string['order'] = 'Порядок';
$string['pluginadministration'] = 'Адміністрування Галереї ViMi';
$string['pluginname'] = 'Галерея ViMi';
$string['privacy:metadata'] = 'Коментарі, які користувачі пишуть до карт, зберігаються. Коли галерея матеріалізує карти з іншої діяльності, збережені копії можуть також містити роботи студентів та їхні імена.';
$string['privacy:metadata:vimigallery_comment'] = 'Коментарі, які користувач публікує до карт у галереї.';
$string['privacy:metadata:vimigallery_comment:content'] = 'Текст коментаря.';
$string['privacy:metadata:vimigallery_comment:timecreated'] = 'Коли коментар було опубліковано.';
$string['privacy:metadata:vimigallery_comment:userid'] = 'Користувач, який опублікував коментар.';
$string['privacy:metadata:vimigallery_item'] = 'Копії карт, матеріалізованих з іншої діяльності, які можуть містити роботи студентів.';
$string['privacy:metadata:vimigallery_item:authorname'] = 'Показуване ім’я автора карти.';
$string['privacy:metadata:vimigallery_item:mapjson'] = 'Збережена копія карти.';
$string['privacy:metadata:vimigallery_item:sourceuserid'] = 'Студент, з роботи якого походить копія.';
$string['privacy:metadata:vimigallery_item_user'] = 'Які студенти зробили внесок у матеріалізовану групову карту.';
$string['privacy:metadata:vimigallery_item_user:userid'] = 'Студент, який зробив внесок у карту.';
$string['profile'] = 'Профіль';
$string['qtypesource'] = 'Тест';
$string['qtypesource_help'] = 'Виберіть діяльність «Тест» у цьому курсі. Зразкові розв’язки її запитань ViMi Pad стануть картами цієї галереї. Зразкові розв’язки показуються наживо лише користувачам, які можуть оцінювати тест.';
$string['refreshsnapshot'] = 'Оновити з джерела';
$string['showauthors'] = 'Показувати імена авторів';
$string['showauthors_help'] = 'Коли ввімкнено, ім’я автора, збережене з кожною картою, показується над нею.';
$string['showtabs'] = 'Показувати вкладки карта/список';
$string['showtabs_help'] = 'Коли ввімкнено, глядачі можуть перемикати кожну карту між графічним виглядом і виглядом списку.';
$string['similarity'] = 'Схожість: {$a}%';
$string['source_datafield'] = 'Поле діяльності «База даних»';
$string['source_qtype'] = 'Діяльність «Тест» (запитання ViMi Pad)';
$string['source_upload'] = 'Завантажені файли';
$string['source_vimipad'] = 'Діяльність ViMi Pad';
$string['sourcefiles'] = 'Файли карт (JSON)';
$string['sourcefiles_help'] = 'Завантажте одну або кілька експортованих карт ViMi Pad у форматі JSON. Кожен файл стає однією картою в галереї.';
$string['sourceheader'] = 'Карти';
$string['sourcemode'] = 'Показати';
$string['sourcemode_help'] = 'Еталон показує зразкові розв’язки запитань ViMi Pad тесту.';
$string['sourcemode_reference'] = 'Зразкові розв’язки';
$string['sourcemode_submissions'] = 'Роботи студентів';
$string['sourcetype'] = 'Джерело карт';
$string['sourcetype_help'] = 'Завантажені файли: одна або кілька експортованих карт JSON. Поле діяльності «База даних»: значення ViMi Pad, збережені в записах діяльності «База даних».';
$string['vimigallery:addinstance'] = 'Додати нову Галерею ViMi';
$string['vimigallery:manageitems'] = 'Курувати карти в Галереї ViMi';
$string['vimigallery:view'] = 'Переглянути Галерею ViMi';
$string['vimipadsource'] = 'Діяльність ViMi Pad';
$string['vimipadsource_help'] = 'Виберіть діяльність ViMi Pad у цьому курсі. У режимі робіт її надіслані карти стають галереєю відповідно до правил доступу діяльності; у режимі еталона її зразковий розв’язок показується оцінювачам.';
$string['visible'] = 'Видимий';
