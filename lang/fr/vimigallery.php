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
 * French language strings for mod_vimigallery.
 *
 * Machine-drafted starter translation for functional and RTL/CJK testing.
 * Not a certified translation; refine via AMOS. English is the source of truth.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcomment'] = 'Ajouter un commentaire';
$string['allowcomments'] = 'Autoriser les commentaires sur les cartes';
$string['allowcomments_help'] = 'Lorsque cette option est activée, les apprenants disposant du droit de commenter peuvent commenter chaque carte de la galerie.';
$string['arrange'] = 'Organiser les cartes';
$string['arrange_help'] = 'Faites glisser une carte par sa poignée pour la réordonner, ou utilisez les flèches haut/bas. Masquez les cartes que vous ne voulez pas montrer aux apprenants.';
$string['author'] = 'Auteur';
$string['backtogallery'] = 'Retour à la galerie';
$string['choosemap'] = 'Choisir une carte...';
$string['comments'] = 'Commentaires';
$string['commentsdisabled'] = 'Les commentaires ne sont pas activés pour cette galerie.';
$string['compare'] = 'Comparer';
$string['compareleft'] = 'Gauche';
$string['compareright'] = 'Droite';
$string['completioncomments'] = 'L’apprenant doit publier des commentaires :';
$string['completioncommentsgroup'] = 'Exiger des commentaires';
$string['completiondetail:comments'] = 'Publier au moins {$a} commentaire(s)';
$string['coupledscroll'] = 'Faire défiler les deux cartes ensemble';
$string['datafieldsource'] = 'Champ de base de données';
$string['datafieldsource_help'] = 'Choisissez un champ ViMi Pad d’une activité Base de données de ce cours. Ses entrées deviennent les cartes de cette galerie, selon les règles d’accès de cette base de données.';
$string['displayheader'] = 'Affichage';
$string['displaymode'] = 'Affichage';
$string['displaymode_course'] = 'Sur la page du cours (intégré, sans lien)';
$string['displaymode_help'] = 'Sur la page du cours intègre la galerie directement dans la section, comme une étiquette. Sur une page séparée affiche un lien et une description, comme une ressource page.';
$string['displaymode_page'] = 'Sur une page séparée (lien et description)';
$string['emptycomment'] = 'Le commentaire est vide.';
$string['enablecompare'] = 'Activer la comparaison côte à côte';
$string['enablecompare_help'] = 'Lorsque cette option est activée, une vue « Comparer » permet aux utilisateurs de placer deux cartes côte à côte et de voir leur degré de similarité.';
$string['freshness'] = 'Fraîcheur';
$string['freshness_help'] = 'En direct lit les entrées actuelles à chaque consultation de la galerie. Statique et instantané enregistrent une copie des cartes actuelles lorsque vous enregistrez l’activité ; actualisez-les ensuite depuis l’onglet organiser.';
$string['freshness_live'] = 'En direct (toujours à jour)';
$string['freshness_snapshot'] = 'Instantané (copie enregistrée)';
$string['freshness_static'] = 'Statique (copie enregistrée)';
$string['invaliditem'] = 'Cette carte n’appartient pas à cette galerie.';
$string['map'] = 'Carte';
$string['mapn'] = 'Carte {$a}';
$string['modulename'] = 'Galerie ViMi';
$string['modulename_help'] = 'La Galerie ViMi affiche une ou plusieurs cartes ViMi Pad en lecture seule. Les apprenants peuvent faire défiler, zoomer et afficher en plein écran.';
$string['modulenameplural'] = 'Galeries ViMi';
$string['nodatafields'] = 'Aucun champ de base de données ViMi Pad dans ce cours';
$string['nomaps'] = 'Cette galerie n’a pas encore de cartes.';
$string['noquizzes'] = 'Aucun test dans ce cours';
$string['novimipads'] = 'Aucune activité ViMi Pad dans ce cours';
$string['order'] = 'Ordre';
$string['pluginadministration'] = 'Administration de la Galerie ViMi';
$string['pluginname'] = 'Galerie ViMi';
$string['privacy:metadata'] = 'Les commentaires que les utilisateurs écrivent sur les cartes sont enregistrés. Lorsqu’une galerie matérialise des cartes d’une autre activité, les copies enregistrées peuvent aussi contenir le travail des apprenants et leurs noms.';
$string['privacy:metadata:vimigallery_comment'] = 'Commentaires qu’un utilisateur publie sur les cartes d’une galerie.';
$string['privacy:metadata:vimigallery_comment:content'] = 'Le texte du commentaire.';
$string['privacy:metadata:vimigallery_comment:timecreated'] = 'Quand le commentaire a été publié.';
$string['privacy:metadata:vimigallery_comment:userid'] = 'L’utilisateur qui a publié le commentaire.';
$string['privacy:metadata:vimigallery_item'] = 'Copies de cartes matérialisées depuis une autre activité, pouvant contenir le travail des apprenants.';
$string['privacy:metadata:vimigallery_item:authorname'] = 'Le nom affiché pour l’auteur de la carte.';
$string['privacy:metadata:vimigallery_item:mapjson'] = 'La copie enregistrée de la carte.';
$string['privacy:metadata:vimigallery_item:sourceuserid'] = 'L’apprenant dont le travail est à l’origine de la copie.';
$string['privacy:metadata:vimigallery_item_user'] = 'Quels apprenants ont contribué à une carte de groupe matérialisée.';
$string['privacy:metadata:vimigallery_item_user:userid'] = 'Un apprenant ayant contribué à la carte.';
$string['profile'] = 'Profil';
$string['qtypesource'] = 'Test';
$string['qtypesource_help'] = 'Choisissez une activité Test de ce cours. Les solutions modèles de ses questions ViMi Pad deviennent les cartes de cette galerie. Les solutions modèles ne sont affichées en direct qu’aux utilisateurs autorisés à évaluer le test.';
$string['refreshsnapshot'] = 'Actualiser depuis la source';
$string['showauthors'] = 'Afficher les noms des auteurs';
$string['showauthors_help'] = 'Lorsque cette option est activée, le nom de l’auteur enregistré avec chaque carte est affiché au-dessus d’elle.';
$string['showtabs'] = 'Afficher les onglets carte/liste';
$string['showtabs_help'] = 'Lorsque cette option est activée, les personnes qui consultent peuvent basculer chaque carte entre la vue graphique et la vue liste.';
$string['similarity'] = 'Similarité : {$a} %';
$string['source_datafield'] = 'Un champ d’activité Base de données';
$string['source_qtype'] = 'Une activité Test (questions ViMi Pad)';
$string['source_upload'] = 'Fichiers téléversés';
$string['source_vimipad'] = 'Une activité ViMi Pad';
$string['sourcefiles'] = 'Fichiers de cartes (JSON)';
$string['sourcefiles_help'] = 'Téléversez une ou plusieurs cartes ViMi Pad exportées sous forme de fichiers JSON. Chaque fichier devient une carte dans la galerie.';
$string['sourceheader'] = 'Cartes';
$string['sourcemode'] = 'Afficher';
$string['sourcemode_help'] = 'Référence affiche les solutions modèles des questions ViMi Pad du test.';
$string['sourcemode_reference'] = 'Solutions modèles';
$string['sourcemode_submissions'] = 'Travaux des apprenants';
$string['sourcetype'] = 'Source des cartes';
$string['sourcetype_help'] = 'Fichiers téléversés : une ou plusieurs cartes JSON exportées. Un champ d’activité Base de données : les valeurs ViMi Pad enregistrées dans les entrées d’une activité Base de données.';
$string['vimigallery:addinstance'] = 'Ajouter une nouvelle Galerie ViMi';
$string['vimigallery:manageitems'] = 'Organiser les cartes d’une Galerie ViMi';
$string['vimigallery:view'] = 'Consulter une Galerie ViMi';
$string['vimipadsource'] = 'Activité ViMi Pad';
$string['vimipadsource_help'] = 'Choisissez une activité ViMi Pad de ce cours. En mode travaux, les cartes soumises deviennent la galerie, selon les règles d’accès de l’activité ; en mode référence, sa solution modèle est affichée aux évaluateurs.';
$string['visible'] = 'Visible';
