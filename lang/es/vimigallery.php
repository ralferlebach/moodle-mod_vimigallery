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
 * Spanish language strings for mod_vimigallery.
 *
 * Machine-drafted starter translation for functional and RTL/CJK testing.
 * Not a certified translation; refine via AMOS. English is the source of truth.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcomment'] = 'Añadir comentario';
$string['allowcomments'] = 'Permitir comentarios en los mapas';
$string['allowcomments_help'] = 'Cuando está activado, los estudiantes con el permiso de comentar pueden comentar cada mapa de la galería.';
$string['arrange'] = 'Organizar mapas';
$string['arrange_help'] = 'Arrastra un mapa por su asa para reordenarlo, o usa las flechas arriba/abajo. Oculta los mapas que no quieras mostrar a los estudiantes.';
$string['author'] = 'Autor';
$string['backtogallery'] = 'Volver a la galería';
$string['choosemap'] = 'Elige un mapa...';
$string['comments'] = 'Comentarios';
$string['commentsdisabled'] = 'Los comentarios no están activados para esta galería.';
$string['compare'] = 'Comparar';
$string['compareleft'] = 'Izquierda';
$string['compareright'] = 'Derecha';
$string['completioncomments'] = 'El estudiante debe publicar comentarios:';
$string['completioncommentsgroup'] = 'Exigir comentarios';
$string['completiondetail:comments'] = 'Publica al menos {$a} comentario(s)';
$string['coupledscroll'] = 'Desplazar ambos mapas a la vez';
$string['datafieldsource'] = 'Campo de base de datos';
$string['datafieldsource_help'] = 'Elige un campo ViMi Pad de una actividad Base de datos de este curso. Sus entradas se convierten en los mapas de esta galería, según las reglas de acceso de esa base de datos.';
$string['displayheader'] = 'Visualización';
$string['displaymode'] = 'Visualización';
$string['displaymode_course'] = 'En la página del curso (incrustada, sin enlace)';
$string['displaymode_help'] = 'En la página del curso incrusta la galería directamente en la sección, como una etiqueta. En una página aparte muestra un enlace y una descripción, como un recurso de página.';
$string['displaymode_page'] = 'En una página aparte (enlace y descripción)';
$string['emptycomment'] = 'El comentario está vacío.';
$string['enablecompare'] = 'Activar la comparación en paralelo';
$string['enablecompare_help'] = 'Cuando está activado, una vista «Comparar» permite a los usuarios colocar dos mapas en paralelo y ver cuánto se parecen.';
$string['freshness'] = 'Actualidad';
$string['freshness_help'] = 'En vivo lee las entradas actuales cada vez que se ve la galería. Estático e instantánea almacenan una copia de los mapas actuales cuando guardas la actividad; actualízalos después desde la pestaña organizar.';
$string['freshness_live'] = 'En vivo (siempre actual)';
$string['freshness_snapshot'] = 'Instantánea (copia almacenada)';
$string['freshness_static'] = 'Estático (copia almacenada)';
$string['invaliditem'] = 'Ese mapa no pertenece a esta galería.';
$string['map'] = 'Mapa';
$string['mapn'] = 'Mapa {$a}';
$string['modulename'] = 'Galería ViMi';
$string['modulename_help'] = 'La Galería ViMi muestra uno o más mapas ViMi Pad en solo lectura. Los estudiantes pueden desplazarse, ampliar y ver a pantalla completa.';
$string['modulenameplural'] = 'Galerías ViMi';
$string['nodatafields'] = 'No hay campos de base de datos ViMi Pad en este curso';
$string['nomaps'] = 'Esta galería aún no tiene mapas.';
$string['noquizzes'] = 'No hay cuestionarios en este curso';
$string['novimipads'] = 'No hay actividades ViMi Pad en este curso';
$string['order'] = 'Orden';
$string['pluginadministration'] = 'Administración de la Galería ViMi';
$string['pluginname'] = 'Galería ViMi';
$string['privacy:metadata'] = 'Se almacenan los comentarios que los usuarios escriben en los mapas. Cuando una galería materializa mapas de otra actividad, las copias almacenadas también pueden contener el trabajo de los estudiantes y sus nombres.';
$string['privacy:metadata:vimigallery_comment'] = 'Comentarios que un usuario publica en los mapas de una galería.';
$string['privacy:metadata:vimigallery_comment:content'] = 'El texto del comentario.';
$string['privacy:metadata:vimigallery_comment:timecreated'] = 'Cuándo se publicó el comentario.';
$string['privacy:metadata:vimigallery_comment:userid'] = 'El usuario que publicó el comentario.';
$string['privacy:metadata:vimigallery_item'] = 'Copias de mapas materializados desde otra actividad, que pueden contener el trabajo de los estudiantes.';
$string['privacy:metadata:vimigallery_item:authorname'] = 'El nombre mostrado para el autor del mapa.';
$string['privacy:metadata:vimigallery_item:mapjson'] = 'La copia almacenada del mapa.';
$string['privacy:metadata:vimigallery_item:sourceuserid'] = 'El estudiante de cuyo trabajo procede la copia.';
$string['privacy:metadata:vimigallery_item_user'] = 'Qué estudiantes contribuyeron a un mapa de grupo materializado.';
$string['privacy:metadata:vimigallery_item_user:userid'] = 'Un estudiante que contribuyó al mapa.';
$string['profile'] = 'Perfil';
$string['qtypesource'] = 'Cuestionario';
$string['qtypesource_help'] = 'Elige una actividad Cuestionario de este curso. Las soluciones modelo de sus preguntas ViMi Pad se convierten en los mapas de esta galería. Las soluciones modelo solo se muestran en vivo a los usuarios que pueden calificar el cuestionario.';
$string['refreshsnapshot'] = 'Actualizar desde la fuente';
$string['showauthors'] = 'Mostrar nombres de autores';
$string['showauthors_help'] = 'Cuando está activado, el nombre del autor almacenado con cada mapa se muestra encima de él.';
$string['showtabs'] = 'Mostrar pestañas mapa/lista';
$string['showtabs_help'] = 'Cuando está activado, quienes ven pueden alternar cada mapa entre la vista gráfica y la vista de lista.';
$string['similarity'] = 'Similitud: {$a}%';
$string['source_datafield'] = 'Un campo de actividad Base de datos';
$string['source_qtype'] = 'Una actividad Cuestionario (preguntas ViMi Pad)';
$string['source_upload'] = 'Archivos subidos';
$string['source_vimipad'] = 'Una actividad ViMi Pad';
$string['sourcefiles'] = 'Archivos de mapas (JSON)';
$string['sourcefiles_help'] = 'Sube uno o más mapas ViMi Pad exportados como archivos JSON. Cada archivo se convierte en un mapa de la galería.';
$string['sourceheader'] = 'Mapas';
$string['sourcemode'] = 'Mostrar';
$string['sourcemode_help'] = 'Referencia muestra las soluciones modelo de las preguntas ViMi Pad del cuestionario.';
$string['sourcemode_reference'] = 'Soluciones modelo';
$string['sourcemode_submissions'] = 'Entregas de los estudiantes';
$string['sourcetype'] = 'Fuente de mapas';
$string['sourcetype_help'] = 'Archivos subidos: uno o más mapas JSON exportados. Un campo de actividad Base de datos: los valores ViMi Pad almacenados en las entradas de una actividad Base de datos.';
$string['vimigallery:addinstance'] = 'Añadir una nueva Galería ViMi';
$string['vimigallery:manageitems'] = 'Curar los mapas de una Galería ViMi';
$string['vimigallery:view'] = 'Ver una Galería ViMi';
$string['vimipadsource'] = 'Actividad ViMi Pad';
$string['vimipadsource_help'] = 'Elige una actividad ViMi Pad de este curso. En modo entregas, sus mapas entregados se convierten en la galería, según las reglas de acceso de la actividad; en modo referencia, su solución modelo se muestra a los evaluadores.';
$string['visible'] = 'Visible';
