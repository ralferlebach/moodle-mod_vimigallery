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
 * Dutch language strings for mod_vimigallery.
 *
 * Machine-drafted starter translation for functional and RTL/CJK testing.
 * Not a certified translation; refine via AMOS. English is the source of truth.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcomment'] = 'Opmerking toevoegen';
$string['allowcomments'] = 'Opmerkingen op kaarten toestaan';
$string['allowcomments_help'] = 'Indien ingeschakeld kunnen deelnemers met de opmerkingsrechten op elke kaart in de galerij reageren.';
$string['arrange'] = 'Kaarten ordenen';
$string['arrange_help'] = 'Sleep een kaart aan de greep om te herordenen, of gebruik de pijlen omhoog/omlaag. Verberg kaarten die je niet aan deelnemers wilt tonen.';
$string['author'] = 'Auteur';
$string['backtogallery'] = 'Terug naar de galerij';
$string['choosemap'] = 'Kies een kaart...';
$string['comments'] = 'Opmerkingen';
$string['commentsdisabled'] = 'Opmerkingen zijn niet ingeschakeld voor deze galerij.';
$string['compare'] = 'Vergelijken';
$string['compareleft'] = 'Links';
$string['compareright'] = 'Rechts';
$string['completioncomments'] = 'Deelnemer moet opmerkingen plaatsen:';
$string['completioncommentsgroup'] = 'Opmerkingen vereisen';
$string['completiondetail:comments'] = 'Plaats ten minste {$a} opmerking(en)';
$string['coupledscroll'] = 'Beide kaarten samen scrollen';
$string['datafieldsource'] = 'Databaseveld';
$string['datafieldsource_help'] = 'Kies een ViMi Pad-veld van een Database-activiteit in deze cursus. De vermeldingen ervan worden de kaarten in deze galerij, volgens de toegangsregels van die database.';
$string['displayheader'] = 'Weergave';
$string['displaymode'] = 'Weergave';
$string['displaymode_course'] = 'Op de cursuspagina (ingesloten, geen link)';
$string['displaymode_help'] = 'Op de cursuspagina sluit de galerij rechtstreeks in de sectie in, zoals een label. Op een aparte pagina toont een link en beschrijving, zoals een paginabron.';
$string['displaymode_page'] = 'Op een aparte pagina (link en beschrijving)';
$string['emptycomment'] = 'De opmerking is leeg.';
$string['enablecompare'] = 'Vergelijking naast elkaar inschakelen';
$string['enablecompare_help'] = 'Indien ingeschakeld kunnen gebruikers via een weergave "Vergelijken" twee kaarten naast elkaar plaatsen en zien hoe gelijkend ze zijn.';
$string['freshness'] = 'Actualiteit';
$string['freshness_help'] = 'Live leest de huidige vermeldingen telkens wanneer de galerij wordt bekeken. Statisch en momentopname slaan een kopie van de huidige kaarten op wanneer je de activiteit opslaat; vernieuw ze later via het tabblad ordenen.';
$string['freshness_live'] = 'Live (altijd actueel)';
$string['freshness_snapshot'] = 'Momentopname (opgeslagen kopie)';
$string['freshness_static'] = 'Statisch (opgeslagen kopie)';
$string['invaliditem'] = 'Die kaart hoort niet bij deze galerij.';
$string['map'] = 'Kaart';
$string['mapn'] = 'Kaart {$a}';
$string['modulename'] = 'ViMi Galerij';
$string['modulename_help'] = 'De ViMi Galerij toont een of meer ViMi Pad-kaarten alleen-lezen. Deelnemers kunnen scrollen, in-/uitzoomen en op volledig scherm bekijken.';
$string['modulenameplural'] = 'ViMi Galerijen';
$string['nodatafields'] = 'Geen ViMi Pad-databasevelden in deze cursus';
$string['nomaps'] = 'Deze galerij heeft nog geen kaarten.';
$string['noquizzes'] = 'Geen toetsen in deze cursus';
$string['novimipads'] = 'Geen ViMi Pad-activiteiten in deze cursus';
$string['order'] = 'Volgorde';
$string['pluginadministration'] = 'ViMi Galerij-beheer';
$string['pluginname'] = 'ViMi Galerij';
$string['privacy:metadata'] = 'Opmerkingen die gebruikers op kaarten schrijven, worden opgeslagen. Wanneer een galerij kaarten uit een andere activiteit materialiseert, kunnen de opgeslagen kopieën ook werk van deelnemers en hun namen bevatten.';
$string['privacy:metadata:vimigallery_comment'] = 'Opmerkingen die een gebruiker op kaarten in een galerij plaatst.';
$string['privacy:metadata:vimigallery_comment:content'] = 'De tekst van de opmerking.';
$string['privacy:metadata:vimigallery_comment:timecreated'] = 'Wanneer de opmerking is geplaatst.';
$string['privacy:metadata:vimigallery_comment:userid'] = 'De gebruiker die de opmerking heeft geplaatst.';
$string['privacy:metadata:vimigallery_item'] = 'Kopieën van kaarten die uit een andere activiteit zijn gematerialiseerd en werk van deelnemers kunnen bevatten.';
$string['privacy:metadata:vimigallery_item:authorname'] = 'De weergavenaam die voor de auteur van de kaart wordt getoond.';
$string['privacy:metadata:vimigallery_item:mapjson'] = 'De opgeslagen kopie van de kaart.';
$string['privacy:metadata:vimigallery_item:sourceuserid'] = 'De deelnemer van wiens werk de kopie afkomstig is.';
$string['privacy:metadata:vimigallery_item_user'] = 'Welke deelnemers hebben bijgedragen aan een gematerialiseerde groepskaart.';
$string['privacy:metadata:vimigallery_item_user:userid'] = 'Een deelnemer die aan de kaart heeft bijgedragen.';
$string['profile'] = 'Profiel';
$string['qtypesource'] = 'Toets';
$string['qtypesource_help'] = 'Kies een Toets-activiteit in deze cursus. De modeloplossingen van de ViMi Pad-vragen worden de kaarten in deze galerij. Modeloplossingen worden alleen live getoond aan gebruikers die de toets mogen beoordelen.';
$string['refreshsnapshot'] = 'Vernieuwen vanaf bron';
$string['showauthors'] = 'Auteursnamen tonen';
$string['showauthors_help'] = 'Indien ingeschakeld wordt de bij elke kaart opgeslagen auteursnaam erboven getoond.';
$string['showtabs'] = 'Tabbladen kaart/lijst tonen';
$string['showtabs_help'] = 'Indien ingeschakeld kunnen kijkers elke kaart wisselen tussen de grafische en de lijstweergave.';
$string['similarity'] = 'Gelijkenis: {$a}%';
$string['source_datafield'] = 'Een veld van een Database-activiteit';
$string['source_qtype'] = 'Een Toets-activiteit (ViMi Pad-vragen)';
$string['source_upload'] = 'Geüploade bestanden';
$string['source_vimipad'] = 'Een ViMi Pad-activiteit';
$string['sourcefiles'] = 'Kaartbestanden (JSON)';
$string['sourcefiles_help'] = 'Upload een of meer geëxporteerde ViMi Pad-kaarten als JSON-bestanden. Elk bestand wordt een kaart in de galerij.';
$string['sourceheader'] = 'Kaarten';
$string['sourcemode'] = 'Tonen';
$string['sourcemode_help'] = 'Referentie toont de modeloplossingen van de ViMi Pad-vragen van de toets.';
$string['sourcemode_reference'] = 'Modeloplossingen';
$string['sourcemode_submissions'] = 'Inzendingen van deelnemers';
$string['sourcetype'] = 'Kaartbron';
$string['sourcetype_help'] = 'Geüploade bestanden: een of meer geëxporteerde JSON-kaarten. Een veld van een Database-activiteit: de ViMi Pad-waarden die over de vermeldingen van een Database-activiteit zijn opgeslagen.';
$string['vimigallery:addinstance'] = 'Een nieuwe ViMi Galerij toevoegen';
$string['vimigallery:manageitems'] = 'De kaarten in een ViMi Galerij cureren';
$string['vimigallery:view'] = 'Een ViMi Galerij bekijken';
$string['vimipadsource'] = 'ViMi Pad-activiteit';
$string['vimipadsource_help'] = 'Kies een ViMi Pad-activiteit in deze cursus. In de inzendingsmodus worden de ingezonden kaarten de galerij, volgens de toegangsregels van de activiteit; in de referentiemodus wordt de modeloplossing aan beoordelaars getoond.';
$string['visible'] = 'Zichtbaar';
