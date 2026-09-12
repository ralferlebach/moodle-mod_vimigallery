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
 * Norwegian Bokmal language strings for mod_vimigallery.
 *
 * Machine-drafted starter translation for functional and RTL/CJK testing.
 * Not a certified translation; refine via AMOS. English is the source of truth.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcomment'] = 'Legg til kommentar';
$string['allowcomments'] = 'Tillat kommentarer på kart';
$string['allowcomments_help'] = 'Når dette er aktivert, kan deltakere med kommentarrettigheten kommentere på hvert kart i galleriet.';
$string['arrange'] = 'Ordne kart';
$string['arrange_help'] = 'Dra et kart i håndtaket for å endre rekkefølge, eller bruk opp/ned-pilene. Skjul kart du ikke vil vise deltakerne.';
$string['author'] = 'Forfatter';
$string['backtogallery'] = 'Tilbake til galleriet';
$string['choosemap'] = 'Velg et kart...';
$string['comments'] = 'Kommentarer';
$string['commentsdisabled'] = 'Kommentarer er ikke aktivert for dette galleriet.';
$string['compare'] = 'Sammenlign';
$string['compareleft'] = 'Venstre';
$string['compareright'] = 'Høyre';
$string['completioncomments'] = 'Deltakeren må skrive kommentarer:';
$string['completioncommentsgroup'] = 'Krev kommentarer';
$string['completiondetail:comments'] = 'Skriv minst {$a} kommentar(er)';
$string['coupledscroll'] = 'Rull begge kart sammen';
$string['datafieldsource'] = 'Databasefelt';
$string['datafieldsource_help'] = 'Velg et ViMi Pad-felt i en Database-aktivitet i dette kurset. Oppføringene blir kartene i dette galleriet, etter tilgangsreglene til den databasen.';
$string['displayheader'] = 'Visning';
$string['displaymode'] = 'Visning';
$string['displaymode_course'] = 'På kurssiden (innebygd, ingen lenke)';
$string['displaymode_help'] = 'På kurssiden bygger galleriet direkte inn i seksjonen, som en etikett. På en egen side viser en lenke og beskrivelse, som en sideressurs.';
$string['displaymode_page'] = 'På en egen side (lenke og beskrivelse)';
$string['emptycomment'] = 'Kommentaren er tom.';
$string['enablecompare'] = 'Aktiver sammenligning side ved side';
$string['enablecompare_help'] = 'Når dette er aktivert, lar en "Sammenlign"-visning brukere plassere to kart side ved side og se hvor like de er.';
$string['freshness'] = 'Aktualitet';
$string['freshness_help'] = 'Live leser de gjeldende oppføringene hver gang galleriet vises. Statisk og øyeblikksbilde lagrer en kopi av de gjeldende kartene når du lagrer aktiviteten; oppdater dem senere fra ordne-fanen.';
$string['freshness_live'] = 'Live (alltid oppdatert)';
$string['freshness_snapshot'] = 'Øyeblikksbilde (lagret kopi)';
$string['freshness_static'] = 'Statisk (lagret kopi)';
$string['invaliditem'] = 'Det kartet hører ikke til dette galleriet.';
$string['map'] = 'Kart';
$string['mapn'] = 'Kart {$a}';
$string['modulename'] = 'ViMi Galleri';
$string['modulename_help'] = 'ViMi Galleriet viser ett eller flere ViMi Pad-kart skrivebeskyttet. Deltakere kan rulle, zoome og vise i fullskjerm.';
$string['modulenameplural'] = 'ViMi Gallerier';
$string['nodatafields'] = 'Ingen ViMi Pad-databasefelter i dette kurset';
$string['nomaps'] = 'Dette galleriet har ingen kart ennå.';
$string['noquizzes'] = 'Ingen quizer i dette kurset';
$string['novimipads'] = 'Ingen ViMi Pad-aktiviteter i dette kurset';
$string['order'] = 'Rekkefølge';
$string['pluginadministration'] = 'ViMi Galleri-administrasjon';
$string['pluginname'] = 'ViMi Galleri';
$string['privacy:metadata'] = 'Kommentarer som brukere skriver på kart, lagres. Når et galleri materialiserer kart fra en annen aktivitet, kan de lagrede kopiene også inneholde deltakeres arbeid og navnene deres.';
$string['privacy:metadata:vimigallery_comment'] = 'Kommentarer en bruker skriver på kart i et galleri.';
$string['privacy:metadata:vimigallery_comment:content'] = 'Teksten i kommentaren.';
$string['privacy:metadata:vimigallery_comment:timecreated'] = 'Når kommentaren ble skrevet.';
$string['privacy:metadata:vimigallery_comment:userid'] = 'Brukeren som skrev kommentaren.';
$string['privacy:metadata:vimigallery_item'] = 'Kopier av kart materialisert fra en annen aktivitet, som kan inneholde deltakeres arbeid.';
$string['privacy:metadata:vimigallery_item:authorname'] = 'Det viste navnet på kartets forfatter.';
$string['privacy:metadata:vimigallery_item:mapjson'] = 'Den lagrede kopien av kartet.';
$string['privacy:metadata:vimigallery_item:sourceuserid'] = 'Deltakeren som kopien stammer fra arbeidet til.';
$string['privacy:metadata:vimigallery_item_user'] = 'Hvilke deltakere som bidro til et materialisert gruppekart.';
$string['privacy:metadata:vimigallery_item_user:userid'] = 'En deltaker som bidro til kartet.';
$string['profile'] = 'Profil';
$string['qtypesource'] = 'Quiz';
$string['qtypesource_help'] = 'Velg en Quiz-aktivitet i dette kurset. Modelløsningene til ViMi Pad-spørsmålene blir kartene i dette galleriet. Modelløsninger vises bare live til brukere som kan vurdere quizen.';
$string['refreshsnapshot'] = 'Oppdater fra kilde';
$string['showauthors'] = 'Vis forfatternavn';
$string['showauthors_help'] = 'Når dette er aktivert, vises forfatternavnet som er lagret med hvert kart, over det.';
$string['showtabs'] = 'Vis fanene kart/liste';
$string['showtabs_help'] = 'Når dette er aktivert, kan de som ser på, veksle hvert kart mellom grafisk visning og listevisning.';
$string['similarity'] = 'Likhet: {$a}%';
$string['source_datafield'] = 'Et felt i en Database-aktivitet';
$string['source_qtype'] = 'En Quiz-aktivitet (ViMi Pad-spørsmål)';
$string['source_upload'] = 'Opplastede filer';
$string['source_vimipad'] = 'En ViMi Pad-aktivitet';
$string['sourcefiles'] = 'Kartfiler (JSON)';
$string['sourcefiles_help'] = 'Last opp ett eller flere eksporterte ViMi Pad-kart som JSON-filer. Hver fil blir ett kart i galleriet.';
$string['sourceheader'] = 'Kart';
$string['sourcemode'] = 'Vis';
$string['sourcemode_help'] = 'Referanse viser modelløsningene til quizens ViMi Pad-spørsmål.';
$string['sourcemode_reference'] = 'Modelløsninger';
$string['sourcemode_submissions'] = 'Deltakernes besvarelser';
$string['sourcetype'] = 'Kartkilde';
$string['sourcetype_help'] = 'Opplastede filer: ett eller flere eksporterte JSON-kart. Et felt i en Database-aktivitet: ViMi Pad-verdiene som er lagret på tvers av oppføringene i en Database-aktivitet.';
$string['vimigallery:addinstance'] = 'Legg til et nytt ViMi Galleri';
$string['vimigallery:manageitems'] = 'Kurater kartene i et ViMi Galleri';
$string['vimigallery:view'] = 'Se et ViMi Galleri';
$string['vimipadsource'] = 'ViMi Pad-aktivitet';
$string['vimipadsource_help'] = 'Velg en ViMi Pad-aktivitet i dette kurset. I innsendingsmodus blir de innsendte kartene til galleriet, etter aktivitetens tilgangsregler; i referansemodus vises modelløsningen til vurderere.';
$string['visible'] = 'Synlig';
