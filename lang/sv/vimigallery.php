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
 * Swedish language strings for mod_vimigallery.
 *
 * Machine-drafted starter translation for functional and RTL/CJK testing.
 * Not a certified translation; refine via AMOS. English is the source of truth.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcomment'] = 'Lägg till kommentar';
$string['allowcomments'] = 'Tillåt kommentarer på kartor';
$string['allowcomments_help'] = 'När detta är aktiverat kan deltagare med kommentarsrättigheten kommentera varje karta i galleriet.';
$string['arrange'] = 'Ordna kartor';
$string['arrange_help'] = 'Dra en karta i handtaget för att ändra ordning, eller använd upp/ned-pilarna. Dölj kartor du inte vill visa för deltagarna.';
$string['author'] = 'Författare';
$string['backtogallery'] = 'Tillbaka till galleriet';
$string['choosemap'] = 'Välj en karta...';
$string['comments'] = 'Kommentarer';
$string['commentsdisabled'] = 'Kommentarer är inte aktiverade för detta galleri.';
$string['compare'] = 'Jämför';
$string['compareleft'] = 'Vänster';
$string['compareright'] = 'Höger';
$string['completioncomments'] = 'Deltagaren måste skriva kommentarer:';
$string['completioncommentsgroup'] = 'Kräv kommentarer';
$string['completiondetail:comments'] = 'Skriv minst {$a} kommentar(er)';
$string['coupledscroll'] = 'Rulla båda kartorna tillsammans';
$string['datafieldsource'] = 'Databasfält';
$string['datafieldsource_help'] = 'Välj ett ViMi Pad-fält i en Databas-aktivitet i denna kurs. Dess poster blir kartorna i detta galleri, enligt den databasens åtkomstregler.';
$string['displayheader'] = 'Visning';
$string['displaymode'] = 'Visning';
$string['displaymode_course'] = 'På kurssidan (inbäddad, ingen länk)';
$string['displaymode_help'] = 'På kurssidan bäddar in galleriet direkt i sektionen, som en etikett. På en separat sida visar en länk och beskrivning, som en sidresurs.';
$string['displaymode_page'] = 'På en separat sida (länk och beskrivning)';
$string['emptycomment'] = 'Kommentaren är tom.';
$string['enablecompare'] = 'Aktivera jämförelse sida vid sida';
$string['enablecompare_help'] = 'När detta är aktiverat låter en "Jämför"-vy användare placera två kartor sida vid sida och se hur lika de är.';
$string['freshness'] = 'Aktualitet';
$string['freshness_help'] = 'Live läser de aktuella posterna varje gång galleriet visas. Statisk och ögonblicksbild sparar en kopia av de aktuella kartorna när du sparar aktiviteten; uppdatera dem senare från fliken ordna.';
$string['freshness_live'] = 'Live (alltid aktuell)';
$string['freshness_snapshot'] = 'Ögonblicksbild (sparad kopia)';
$string['freshness_static'] = 'Statisk (sparad kopia)';
$string['invaliditem'] = 'Den kartan tillhör inte detta galleri.';
$string['map'] = 'Karta';
$string['mapn'] = 'Karta {$a}';
$string['modulename'] = 'ViMi Galleri';
$string['modulename_help'] = 'ViMi Galleriet visar en eller flera ViMi Pad-kartor skrivskyddat. Deltagare kan rulla, zooma och visa i helskärm.';
$string['modulenameplural'] = 'ViMi Gallerier';
$string['nodatafields'] = 'Inga ViMi Pad-databasfält i denna kurs';
$string['nomaps'] = 'Detta galleri har inga kartor ännu.';
$string['noquizzes'] = 'Inga quiz i denna kurs';
$string['novimipads'] = 'Inga ViMi Pad-aktiviteter i denna kurs';
$string['order'] = 'Ordning';
$string['pluginadministration'] = 'ViMi Galleri-administration';
$string['pluginname'] = 'ViMi Galleri';
$string['privacy:metadata'] = 'Kommentarer som användare skriver på kartor sparas. När ett galleri materialiserar kartor från en annan aktivitet kan de sparade kopiorna även innehålla deltagares arbete och deras namn.';
$string['privacy:metadata:vimigallery_comment'] = 'Kommentarer som en användare skriver på kartor i ett galleri.';
$string['privacy:metadata:vimigallery_comment:content'] = 'Kommentarens text.';
$string['privacy:metadata:vimigallery_comment:timecreated'] = 'När kommentaren skrevs.';
$string['privacy:metadata:vimigallery_comment:userid'] = 'Användaren som skrev kommentaren.';
$string['privacy:metadata:vimigallery_item'] = 'Kopior av kartor materialiserade från en annan aktivitet, som kan innehålla deltagares arbete.';
$string['privacy:metadata:vimigallery_item:authorname'] = 'Det visade namnet för kartans författare.';
$string['privacy:metadata:vimigallery_item:mapjson'] = 'Den sparade kopian av kartan.';
$string['privacy:metadata:vimigallery_item:sourceuserid'] = 'Deltagaren vars arbete kopian härrör från.';
$string['privacy:metadata:vimigallery_item_user'] = 'Vilka deltagare som bidrog till en materialiserad gruppkarta.';
$string['privacy:metadata:vimigallery_item_user:userid'] = 'En deltagare som bidrog till kartan.';
$string['profile'] = 'Profil';
$string['qtypesource'] = 'Quiz';
$string['qtypesource_help'] = 'Välj en Quiz-aktivitet i denna kurs. Modellösningarna till dess ViMi Pad-frågor blir kartorna i detta galleri. Modellösningar visas endast live för användare som får bedöma quizet.';
$string['refreshsnapshot'] = 'Uppdatera från källa';
$string['showauthors'] = 'Visa författarnamn';
$string['showauthors_help'] = 'När detta är aktiverat visas författarnamnet som sparats med varje karta ovanför den.';
$string['showtabs'] = 'Visa flikarna karta/lista';
$string['showtabs_help'] = 'När detta är aktiverat kan tittare växla varje karta mellan den grafiska vyn och listvyn.';
$string['similarity'] = 'Likhet: {$a}%';
$string['source_datafield'] = 'Ett fält i en Databas-aktivitet';
$string['source_qtype'] = 'En Quiz-aktivitet (ViMi Pad-frågor)';
$string['source_upload'] = 'Uppladdade filer';
$string['source_vimipad'] = 'En ViMi Pad-aktivitet';
$string['sourcefiles'] = 'Kartfiler (JSON)';
$string['sourcefiles_help'] = 'Ladda upp en eller flera exporterade ViMi Pad-kartor som JSON-filer. Varje fil blir en karta i galleriet.';
$string['sourceheader'] = 'Kartor';
$string['sourcemode'] = 'Visa';
$string['sourcemode_help'] = 'Referens visar modellösningarna till quizets ViMi Pad-frågor.';
$string['sourcemode_reference'] = 'Modellösningar';
$string['sourcemode_submissions'] = 'Deltagarnas inlämningar';
$string['sourcetype'] = 'Kartkälla';
$string['sourcetype_help'] = 'Uppladdade filer: en eller flera exporterade JSON-kartor. Ett fält i en Databas-aktivitet: de ViMi Pad-värden som sparats över posterna i en Databas-aktivitet.';
$string['vimigallery:addinstance'] = 'Lägg till ett nytt ViMi Galleri';
$string['vimigallery:manageitems'] = 'Kurera kartorna i ett ViMi Galleri';
$string['vimigallery:view'] = 'Visa ett ViMi Galleri';
$string['vimipadsource'] = 'ViMi Pad-aktivitet';
$string['vimipadsource_help'] = 'Välj en ViMi Pad-aktivitet i denna kurs. I inlämningsläge blir dess inlämnade kartor galleriet, enligt aktivitetens åtkomstregler; i referensläge visas dess modellösning för bedömare.';
$string['visible'] = 'Synlig';
