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
 * Italian language strings for mod_vimigallery.
 *
 * Machine-drafted starter translation for functional and RTL/CJK testing.
 * Not a certified translation; refine via AMOS. English is the source of truth.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcomment'] = 'Aggiungi commento';
$string['allowcomments'] = 'Consenti commenti sulle mappe';
$string['allowcomments_help'] = 'Quando è attivo, gli studenti con il permesso di commento possono commentare ogni mappa della galleria.';
$string['arrange'] = 'Disponi mappe';
$string['arrange_help'] = 'Trascina una mappa dalla maniglia per riordinarla, oppure usa le frecce su/giù. Nascondi le mappe che non vuoi mostrare agli studenti.';
$string['author'] = 'Autore';
$string['backtogallery'] = 'Torna alla galleria';
$string['choosemap'] = 'Scegli una mappa...';
$string['comments'] = 'Commenti';
$string['commentsdisabled'] = 'I commenti non sono attivati per questa galleria.';
$string['compare'] = 'Confronta';
$string['compareleft'] = 'Sinistra';
$string['compareright'] = 'Destra';
$string['completioncomments'] = 'Lo studente deve pubblicare commenti:';
$string['completioncommentsgroup'] = 'Richiedi commenti';
$string['completiondetail:comments'] = 'Pubblica almeno {$a} commento/i';
$string['coupledscroll'] = 'Scorri entrambe le mappe insieme';
$string['datafieldsource'] = 'Campo del database';
$string['datafieldsource_help'] = 'Scegli un campo ViMi Pad di un’attività Database in questo corso. Le sue voci diventano le mappe di questa galleria, secondo le regole di accesso di quel database.';
$string['displayheader'] = 'Visualizzazione';
$string['displaymode'] = 'Visualizzazione';
$string['displaymode_course'] = 'Nella pagina del corso (incorporata, senza link)';
$string['displaymode_help'] = 'Nella pagina del corso incorpora la galleria direttamente nella sezione, come un’etichetta. In una pagina separata mostra un link e una descrizione, come una risorsa pagina.';
$string['displaymode_page'] = 'In una pagina separata (link e descrizione)';
$string['emptycomment'] = 'Il commento è vuoto.';
$string['enablecompare'] = 'Attiva il confronto affiancato';
$string['enablecompare_help'] = 'Quando è attivo, una vista "Confronta" consente agli utenti di affiancare due mappe e vedere quanto sono simili.';
$string['freshness'] = 'Aggiornamento';
$string['freshness_help'] = 'Live legge le voci correnti a ogni visualizzazione della galleria. Statico e istantanea memorizzano una copia delle mappe correnti quando salvi l’attività; aggiornale in seguito dalla scheda disponi.';
$string['freshness_live'] = 'Live (sempre aggiornata)';
$string['freshness_snapshot'] = 'Istantanea (copia memorizzata)';
$string['freshness_static'] = 'Statico (copia memorizzata)';
$string['invaliditem'] = 'Quella mappa non appartiene a questa galleria.';
$string['map'] = 'Mappa';
$string['mapn'] = 'Mappa {$a}';
$string['modulename'] = 'Galleria ViMi';
$string['modulename_help'] = 'La Galleria ViMi mostra una o più mappe ViMi Pad in sola lettura. Gli studenti possono scorrere, ingrandire e visualizzare a schermo intero.';
$string['modulenameplural'] = 'Gallerie ViMi';
$string['nodatafields'] = 'Nessun campo di database ViMi Pad in questo corso';
$string['nomaps'] = 'Questa galleria non ha ancora mappe.';
$string['noquizzes'] = 'Nessun quiz in questo corso';
$string['novimipads'] = 'Nessuna attività ViMi Pad in questo corso';
$string['order'] = 'Ordine';
$string['pluginadministration'] = 'Amministrazione Galleria ViMi';
$string['pluginname'] = 'Galleria ViMi';
$string['privacy:metadata'] = 'I commenti che gli utenti scrivono sulle mappe vengono memorizzati. Quando una galleria materializza mappe da un’altra attività, le copie memorizzate possono contenere anche il lavoro degli studenti e i loro nomi.';
$string['privacy:metadata:vimigallery_comment'] = 'Commenti che un utente pubblica sulle mappe di una galleria.';
$string['privacy:metadata:vimigallery_comment:content'] = 'Il testo del commento.';
$string['privacy:metadata:vimigallery_comment:timecreated'] = 'Quando è stato pubblicato il commento.';
$string['privacy:metadata:vimigallery_comment:userid'] = 'L’utente che ha pubblicato il commento.';
$string['privacy:metadata:vimigallery_item'] = 'Copie di mappe materializzate da un’altra attività, che possono contenere il lavoro degli studenti.';
$string['privacy:metadata:vimigallery_item:authorname'] = 'Il nome visualizzato per l’autore della mappa.';
$string['privacy:metadata:vimigallery_item:mapjson'] = 'La copia memorizzata della mappa.';
$string['privacy:metadata:vimigallery_item:sourceuserid'] = 'Lo studente dal cui lavoro deriva la copia.';
$string['privacy:metadata:vimigallery_item_user'] = 'Quali studenti hanno contribuito a una mappa di gruppo materializzata.';
$string['privacy:metadata:vimigallery_item_user:userid'] = 'Uno studente che ha contribuito alla mappa.';
$string['profile'] = 'Profilo';
$string['qtypesource'] = 'Quiz';
$string['qtypesource_help'] = 'Scegli un’attività Quiz in questo corso. Le soluzioni modello delle sue domande ViMi Pad diventano le mappe di questa galleria. Le soluzioni modello sono mostrate live solo agli utenti che possono valutare il quiz.';
$string['refreshsnapshot'] = 'Aggiorna dalla sorgente';
$string['showauthors'] = 'Mostra i nomi degli autori';
$string['showauthors_help'] = 'Quando è attivo, il nome dell’autore memorizzato con ogni mappa viene mostrato sopra di essa.';
$string['showtabs'] = 'Mostra le schede mappa/elenco';
$string['showtabs_help'] = 'Quando è attivo, chi visualizza può alternare ogni mappa tra la vista grafica e la vista elenco.';
$string['similarity'] = 'Somiglianza: {$a}%';
$string['source_datafield'] = 'Un campo di un’attività Database';
$string['source_qtype'] = 'Un’attività Quiz (domande ViMi Pad)';
$string['source_upload'] = 'File caricati';
$string['source_vimipad'] = 'Un’attività ViMi Pad';
$string['sourcefiles'] = 'File delle mappe (JSON)';
$string['sourcefiles_help'] = 'Carica una o più mappe ViMi Pad esportate come file JSON. Ogni file diventa una mappa nella galleria.';
$string['sourceheader'] = 'Mappe';
$string['sourcemode'] = 'Mostra';
$string['sourcemode_help'] = 'Riferimento mostra le soluzioni modello delle domande ViMi Pad del quiz.';
$string['sourcemode_reference'] = 'Soluzioni modello';
$string['sourcemode_submissions'] = 'Consegne degli studenti';
$string['sourcetype'] = 'Sorgente delle mappe';
$string['sourcetype_help'] = 'File caricati: una o più mappe JSON esportate. Un campo di un’attività Database: i valori ViMi Pad memorizzati nelle voci di un’attività Database.';
$string['vimigallery:addinstance'] = 'Aggiungi una nuova Galleria ViMi';
$string['vimigallery:manageitems'] = 'Cura le mappe di una Galleria ViMi';
$string['vimigallery:view'] = 'Visualizza una Galleria ViMi';
$string['vimipadsource'] = 'Attività ViMi Pad';
$string['vimipadsource_help'] = 'Scegli un’attività ViMi Pad in questo corso. In modalità consegne le mappe consegnate diventano la galleria, secondo le regole di accesso dell’attività; in modalità riferimento la sua soluzione modello è mostrata ai valutatori.';
$string['visible'] = 'Visibile';
