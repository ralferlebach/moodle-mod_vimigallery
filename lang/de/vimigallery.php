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
 * German strings for mod_vimigallery.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcomment'] = 'Kommentar hinzufügen';
$string['allowcomments'] = 'Kommentare zu Maps erlauben';
$string['allowcomments_help'] = 'Wenn aktiviert, können Lernende mit der Kommentar-Berechtigung jede Map der Galerie kommentieren.';
$string['arrange'] = 'Maps anordnen';
$string['arrange_help'] = 'Ziehen Sie eine Map am Griff, um sie umzusortieren, oder nutzen Sie die Pfeile. Blenden Sie Maps aus, die Lernende nicht sehen sollen.';
$string['author'] = 'Autor';
$string['backtogallery'] = 'Zurück zur Galerie';
$string['choosemap'] = 'Map wählen...';
$string['comments'] = 'Kommentare';
$string['commentsdisabled'] = 'Kommentare sind für diese Galerie nicht aktiviert.';
$string['compare'] = 'Vergleichen';
$string['compareleft'] = 'Links';
$string['compareright'] = 'Rechts';
$string['completioncomments'] = 'Lernende müssen Kommentare schreiben:';
$string['completioncommentsgroup'] = 'Kommentare verlangen';
$string['completiondetail:comments'] = 'Mindestens {$a} Kommentar(e) schreiben';
$string['coupledscroll'] = 'Beide Maps gemeinsam scrollen';
$string['datafieldsource'] = 'Datenbankfeld';
$string['datafieldsource_help'] = 'Wählen Sie ein ViMi-Pad-Feld einer Datenbank-Aktivität in diesem Kurs. Dessen Einträge werden zu den Maps dieser Galerie - nach den Zugriffsregeln dieser Datenbank.';
$string['displayheader'] = 'Anzeige';
$string['displaymode'] = 'Anzeige';
$string['displaymode_course'] = 'Auf der Kursseite (eingebettet, ohne Link)';
$string['displaymode_help'] = 'Auf der Kursseite bettet die Galerie direkt in den Abschnitt ein, wie eine Textseite (Label). Auf eigener Seite zeigt einen Link mit Beschreibung, wie eine Textseite (Page).';
$string['displaymode_page'] = 'Auf eigener Seite (Link und Beschreibung)';
$string['emptycomment'] = 'Der Kommentar ist leer.';
$string['enablecompare'] = 'Vergleich nebeneinander aktivieren';
$string['enablecompare_help'] = 'Wenn aktiviert, können über eine Vergleichsansicht zwei Maps nebeneinander gelegt und ihre Ähnlichkeit angezeigt werden.';
$string['freshness'] = 'Aktualität';
$string['freshness_help'] = 'Live liest die aktuellen Einträge bei jedem Aufruf. Statisch und Snapshot speichern beim Speichern der Aktivität eine Kopie der aktuellen Maps; im Anordnen-Reiter später aktualisierbar.';
$string['freshness_live'] = 'Live (immer aktuell)';
$string['freshness_snapshot'] = 'Snapshot (gespeicherte Kopie)';
$string['freshness_static'] = 'Statisch (gespeicherte Kopie)';
$string['invaliditem'] = 'Diese Map gehört nicht zu dieser Galerie.';
$string['map'] = 'Map';
$string['mapn'] = 'Map {$a}';
$string['modulename'] = 'ViMi-Galerie';
$string['modulename_help'] = 'Die ViMi-Galerie zeigt eine oder mehrere ViMi-Pad-Maps schreibgeschützt. Lernende können scrollen, zoomen und im Vollbild ansehen.';
$string['modulenameplural'] = 'ViMi-Galerien';
$string['nodatafields'] = 'Keine ViMi-Pad-Datenbankfelder in diesem Kurs';
$string['nomaps'] = 'Diese Galerie enthält noch keine Maps.';
$string['noquizzes'] = 'Keine Tests in diesem Kurs';
$string['novimipads'] = 'Keine ViMi-Pad-Aktivitäten in diesem Kurs';
$string['novimipads'] = 'Keine ViMi-Pad-Aktivitäten in diesem Kurs';
$string['order'] = 'Reihenfolge';
$string['pluginadministration'] = 'ViMi-Galerie-Administration';
$string['pluginname'] = 'ViMi-Galerie';
$string['privacy:metadata'] = 'Kommentare, die Nutzende zu Maps schreiben, werden gespeichert. Wenn eine Galerie Maps aus einer anderen Aktivität materialisiert, können die gespeicherten Kopien auch Arbeiten von Lernenden und deren Namen enthalten.';
$string['privacy:metadata:vimigallery_comment'] = 'Kommentare, die eine Person zu Maps einer Galerie schreibt.';
$string['privacy:metadata:vimigallery_comment:content'] = 'Der Text des Kommentars.';
$string['privacy:metadata:vimigallery_comment:timecreated'] = 'Wann der Kommentar geschrieben wurde.';
$string['privacy:metadata:vimigallery_comment:userid'] = 'Die Person, die den Kommentar geschrieben hat.';
$string['privacy:metadata:vimigallery_item'] = 'Kopien von Maps, die aus einer anderen Aktivität materialisiert wurden und Arbeiten von Lernenden enthalten können.';
$string['privacy:metadata:vimigallery_item:authorname'] = 'Der angezeigte Name der Autorin oder des Autors.';
$string['privacy:metadata:vimigallery_item:mapjson'] = 'Die gespeicherte Kopie der Map.';
$string['privacy:metadata:vimigallery_item:sourceuserid'] = 'Die lernende Person, von deren Arbeit die Kopie stammt.';
$string['profile'] = 'Profil';
$string['qtypesource'] = 'Test';
$string['qtypesource_help'] = 'Wählen Sie eine Test-Aktivität in diesem Kurs. Die Musterlösungen ihrer ViMi-Pad-Fragen werden zu den Maps dieser Galerie. Musterlösungen werden live nur Personen gezeigt, die den Test bewerten dürfen.';
$string['refreshsnapshot'] = 'Aus Quelle aktualisieren';
$string['showauthors'] = 'Autorennamen anzeigen';
$string['showauthors_help'] = 'Wenn aktiviert, wird der zu jeder Map gespeicherte Autorenname darüber angezeigt.';
$string['showtabs'] = 'Reiter Map/Liste anzeigen';
$string['showtabs_help'] = 'Wenn aktiviert, können Betrachtende jede Map zwischen grafischer und Listenansicht umschalten.';
$string['similarity'] = 'Ähnlichkeit: {$a}%';
$string['source_datafield'] = 'Feld einer Datenbank-Aktivität';
$string['source_qtype'] = 'Eine Test-Aktivität (ViMi-Pad-Fragen)';
$string['source_upload'] = 'Hochgeladene Dateien';
$string['source_vimipad'] = 'Eine ViMi-Pad-Aktivität';
$string['source_vimipad'] = 'Eine ViMi-Pad-Aktivität';
$string['sourcefiles'] = 'Map-Dateien (JSON)';
$string['sourcefiles_help'] = 'Laden Sie eine oder mehrere exportierte ViMi-Pad-Maps als JSON-Dateien hoch. Jede Datei wird zu einer Map in der Galerie.';
$string['sourceheader'] = 'Maps';
$string['sourcemode'] = 'Anzeigen';
$string['sourcemode_help'] = 'Referenz zeigt die Musterlösungen der ViMi-Pad-Fragen des Tests.';
$string['sourcemode_reference'] = 'Musterlösungen';
$string['sourcemode_submissions'] = 'Abgaben der Lernenden';
$string['sourcemode_submissions'] = 'Abgaben der Lernenden';
$string['sourcetype'] = 'Map-Quelle';
$string['sourcetype_help'] = 'Hochgeladene Dateien: eine oder mehrere exportierte JSON-Maps. Feld einer Datenbank-Aktivität: die ViMi-Pad-Werte über die Einträge einer Datenbank-Aktivität.';
$string['vimigallery:addinstance'] = 'Eine neue ViMi-Galerie hinzufügen';
$string['vimigallery:manageitems'] = 'Die Maps einer ViMi-Galerie kuratieren';
$string['vimigallery:view'] = 'Eine ViMi-Galerie ansehen';
$string['vimipadsource'] = 'ViMi-Pad-Aktivität';
$string['vimipadsource'] = 'ViMi-Pad-Aktivität';
$string['vimipadsource_help'] = 'Wählen Sie eine ViMi-Pad-Aktivität in diesem Kurs. Im Abgaben-Modus werden ihre abgegebenen Maps zur Galerie - nach den Zugriffsregeln der Aktivität; im Referenz-Modus wird ihre Musterlösung Bewertenden gezeigt.';
$string['vimipadsource_help'] = 'Wählen Sie eine ViMi-Pad-Aktivität in diesem Kurs. Zeigen Sie entweder deren Musterlösung oder die abgegebenen Maps. Abgaben folgen den Zugriffsregeln der Aktivität: Lernende sehen nur eigene und die ihrer Gruppen, Bewertende sehen alle.';
$string['visible'] = 'Sichtbar';
