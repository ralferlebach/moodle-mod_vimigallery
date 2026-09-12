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
 * Danish language strings for mod_vimigallery.
 *
 * Machine-drafted starter translation for functional and RTL/CJK testing.
 * Not a certified translation; refine via AMOS. English is the source of truth.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcomment'] = 'Tilføj kommentar';
$string['allowcomments'] = 'Tillad kommentarer på kort';
$string['allowcomments_help'] = 'Når dette er aktiveret, kan studerende med kommentarrettigheden kommentere på hvert kort i galleriet.';
$string['arrange'] = 'Arranger kort';
$string['arrange_help'] = 'Træk et kort i håndtaget for at ændre rækkefølgen, eller brug op/ned-pilene. Skjul kort, du ikke vil vise de studerende.';
$string['author'] = 'Forfatter';
$string['backtogallery'] = 'Tilbage til galleriet';
$string['choosemap'] = 'Vælg et kort...';
$string['comments'] = 'Kommentarer';
$string['commentsdisabled'] = 'Kommentarer er ikke aktiveret for dette galleri.';
$string['compare'] = 'Sammenlign';
$string['compareleft'] = 'Venstre';
$string['compareright'] = 'Højre';
$string['completioncomments'] = 'Den studerende skal skrive kommentarer:';
$string['completioncommentsgroup'] = 'Kræv kommentarer';
$string['completiondetail:comments'] = 'Skriv mindst {$a} kommentar(er)';
$string['coupledscroll'] = 'Rul begge kort sammen';
$string['datafieldsource'] = 'Databasefelt';
$string['datafieldsource_help'] = 'Vælg et ViMi Pad-felt i en Database-aktivitet i dette kursus. Dets poster bliver kortene i dette galleri i henhold til den databases adgangsregler.';
$string['displayheader'] = 'Visning';
$string['displaymode'] = 'Visning';
$string['displaymode_course'] = 'På kursussiden (indlejret, ingen link)';
$string['displaymode_help'] = 'På kursussiden indlejrer galleriet direkte i sektionen, som en etiket. På en separat side viser et link og en beskrivelse, som en sideressource.';
$string['displaymode_page'] = 'På en separat side (link og beskrivelse)';
$string['emptycomment'] = 'Kommentaren er tom.';
$string['enablecompare'] = 'Aktivér sammenligning side om side';
$string['enablecompare_help'] = 'Når dette er aktiveret, lader en "Sammenlign"-visning brugerne placere to kort side om side og se, hvor ens de er.';
$string['freshness'] = 'Aktualitet';
$string['freshness_help'] = 'Live læser de aktuelle poster, hver gang galleriet vises. Statisk og øjebliksbillede gemmer en kopi af de aktuelle kort, når du gemmer aktiviteten; opdater dem senere fra arranger-fanen.';
$string['freshness_live'] = 'Live (altid aktuel)';
$string['freshness_snapshot'] = 'Øjebliksbillede (gemt kopi)';
$string['freshness_static'] = 'Statisk (gemt kopi)';
$string['invaliditem'] = 'Det kort hører ikke til dette galleri.';
$string['map'] = 'Kort';
$string['mapn'] = 'Kort {$a}';
$string['modulename'] = 'ViMi Galleri';
$string['modulename_help'] = 'ViMi Galleriet viser et eller flere ViMi Pad-kort skrivebeskyttet. Studerende kan rulle, zoome og se i fuld skærm.';
$string['modulenameplural'] = 'ViMi Gallerier';
$string['nodatafields'] = 'Ingen ViMi Pad-databasefelter i dette kursus';
$string['nomaps'] = 'Dette galleri har endnu ingen kort.';
$string['noquizzes'] = 'Ingen quizzer i dette kursus';
$string['novimipads'] = 'Ingen ViMi Pad-aktiviteter i dette kursus';
$string['order'] = 'Rækkefølge';
$string['pluginadministration'] = 'ViMi Galleri-administration';
$string['pluginname'] = 'ViMi Galleri';
$string['privacy:metadata'] = 'Kommentarer, som brugere skriver på kort, gemmes. Når et galleri materialiserer kort fra en anden aktivitet, kan de gemte kopier også indeholde studerendes arbejde og deres navne.';
$string['privacy:metadata:vimigallery_comment'] = 'Kommentarer, som en bruger skriver på kort i et galleri.';
$string['privacy:metadata:vimigallery_comment:content'] = 'Kommentarens tekst.';
$string['privacy:metadata:vimigallery_comment:timecreated'] = 'Hvornår kommentaren blev skrevet.';
$string['privacy:metadata:vimigallery_comment:userid'] = 'Brugeren, der skrev kommentaren.';
$string['privacy:metadata:vimigallery_item'] = 'Kopier af kort materialiseret fra en anden aktivitet, som kan indeholde studerendes arbejde.';
$string['privacy:metadata:vimigallery_item:authorname'] = 'Det viste navn for kortets forfatter.';
$string['privacy:metadata:vimigallery_item:mapjson'] = 'Den gemte kopi af kortet.';
$string['privacy:metadata:vimigallery_item:sourceuserid'] = 'Den studerende, hvis arbejde kopien stammer fra.';
$string['privacy:metadata:vimigallery_item_user'] = 'Hvilke studerende der bidrog til et materialiseret gruppekort.';
$string['privacy:metadata:vimigallery_item_user:userid'] = 'En studerende, der bidrog til kortet.';
$string['profile'] = 'Profil';
$string['qtypesource'] = 'Quiz';
$string['qtypesource_help'] = 'Vælg en Quiz-aktivitet i dette kursus. Modelløsningerne til dens ViMi Pad-spørgsmål bliver kortene i dette galleri. Modelløsninger vises kun live til brugere, der må bedømme quizzen.';
$string['refreshsnapshot'] = 'Opdater fra kilde';
$string['showauthors'] = 'Vis forfatternavne';
$string['showauthors_help'] = 'Når dette er aktiveret, vises det forfatternavn, der er gemt med hvert kort, oven over det.';
$string['showtabs'] = 'Vis fanerne kort/liste';
$string['showtabs_help'] = 'Når dette er aktiveret, kan seere skifte hvert kort mellem den grafiske visning og listevisningen.';
$string['similarity'] = 'Lighed: {$a}%';
$string['source_datafield'] = 'Et felt i en Database-aktivitet';
$string['source_qtype'] = 'En Quiz-aktivitet (ViMi Pad-spørgsmål)';
$string['source_upload'] = 'Uploadede filer';
$string['source_vimipad'] = 'En ViMi Pad-aktivitet';
$string['sourcefiles'] = 'Kortfiler (JSON)';
$string['sourcefiles_help'] = 'Upload et eller flere eksporterede ViMi Pad-kort som JSON-filer. Hver fil bliver ét kort i galleriet.';
$string['sourceheader'] = 'Kort';
$string['sourcemode'] = 'Vis';
$string['sourcemode_help'] = 'Reference viser modelløsningerne til quizzens ViMi Pad-spørgsmål.';
$string['sourcemode_reference'] = 'Modelløsninger';
$string['sourcemode_submissions'] = 'Studerendes besvarelser';
$string['sourcetype'] = 'Kortkilde';
$string['sourcetype_help'] = 'Uploadede filer: et eller flere eksporterede JSON-kort. Et felt i en Database-aktivitet: de ViMi Pad-værdier, der er gemt på tværs af posterne i en Database-aktivitet.';
$string['vimigallery:addinstance'] = 'Tilføj et nyt ViMi Galleri';
$string['vimigallery:manageitems'] = 'Kurater kortene i et ViMi Galleri';
$string['vimigallery:view'] = 'Se et ViMi Galleri';
$string['vimipadsource'] = 'ViMi Pad-aktivitet';
$string['vimipadsource_help'] = 'Vælg en ViMi Pad-aktivitet i dette kursus. I besvarelsestilstand bliver dens indsendte kort til galleriet i henhold til aktivitetens adgangsregler; i referencetilstand vises dens modelløsning til bedømmere.';
$string['visible'] = 'Synlig';
