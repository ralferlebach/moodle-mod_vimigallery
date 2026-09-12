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
 * Portuguese language strings for mod_vimigallery.
 *
 * Machine-drafted starter translation for functional and RTL/CJK testing.
 * Not a certified translation; refine via AMOS. English is the source of truth.
 *
 * @package    mod_vimigallery
 * @copyright  2026 Ralf Erlebach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcomment'] = 'Adicionar comentário';
$string['allowcomments'] = 'Permitir comentários nos mapas';
$string['allowcomments_help'] = 'Quando ativado, os estudantes com a permissão de comentar podem comentar cada mapa da galeria.';
$string['arrange'] = 'Organizar mapas';
$string['arrange_help'] = 'Arraste um mapa pela pega para reordenar, ou use as setas para cima/baixo. Oculte os mapas que não quer mostrar aos estudantes.';
$string['author'] = 'Autor';
$string['backtogallery'] = 'Voltar à galeria';
$string['choosemap'] = 'Escolha um mapa...';
$string['comments'] = 'Comentários';
$string['commentsdisabled'] = 'Os comentários não estão ativados para esta galeria.';
$string['compare'] = 'Comparar';
$string['compareleft'] = 'Esquerda';
$string['compareright'] = 'Direita';
$string['completioncomments'] = 'O estudante deve publicar comentários:';
$string['completioncommentsgroup'] = 'Exigir comentários';
$string['completiondetail:comments'] = 'Publique pelo menos {$a} comentário(s)';
$string['coupledscroll'] = 'Deslocar ambos os mapas em conjunto';
$string['datafieldsource'] = 'Campo de base de dados';
$string['datafieldsource_help'] = 'Escolha um campo ViMi Pad de uma atividade Base de dados deste curso. As suas entradas tornam-se os mapas desta galeria, seguindo as regras de acesso dessa base de dados.';
$string['displayheader'] = 'Apresentação';
$string['displaymode'] = 'Apresentação';
$string['displaymode_course'] = 'Na página do curso (incorporada, sem ligação)';
$string['displaymode_help'] = 'Na página do curso incorpora a galeria diretamente na secção, como um rótulo. Numa página separada mostra uma ligação e descrição, como um recurso de página.';
$string['displaymode_page'] = 'Numa página separada (ligação e descrição)';
$string['emptycomment'] = 'O comentário está vazio.';
$string['enablecompare'] = 'Ativar a comparação lado a lado';
$string['enablecompare_help'] = 'Quando ativado, uma vista «Comparar» permite aos utilizadores colocar dois mapas lado a lado e ver o quão semelhantes são.';
$string['freshness'] = 'Atualidade';
$string['freshness_help'] = 'Ao vivo lê as entradas atuais sempre que a galeria é vista. Estático e instantâneo guardam uma cópia dos mapas atuais quando guarda a atividade; atualize-os depois no separador organizar.';
$string['freshness_live'] = 'Ao vivo (sempre atual)';
$string['freshness_snapshot'] = 'Instantâneo (cópia guardada)';
$string['freshness_static'] = 'Estático (cópia guardada)';
$string['invaliditem'] = 'Esse mapa não pertence a esta galeria.';
$string['map'] = 'Mapa';
$string['mapn'] = 'Mapa {$a}';
$string['modulename'] = 'Galeria ViMi';
$string['modulename_help'] = 'A Galeria ViMi mostra um ou mais mapas ViMi Pad apenas para leitura. Os estudantes podem deslocar, ampliar e ver em ecrã inteiro.';
$string['modulenameplural'] = 'Galerias ViMi';
$string['nodatafields'] = 'Nenhum campo de base de dados ViMi Pad neste curso';
$string['nomaps'] = 'Esta galeria ainda não tem mapas.';
$string['noquizzes'] = 'Nenhum teste neste curso';
$string['novimipads'] = 'Nenhuma atividade ViMi Pad neste curso';
$string['order'] = 'Ordem';
$string['pluginadministration'] = 'Administração da Galeria ViMi';
$string['pluginname'] = 'Galeria ViMi';
$string['privacy:metadata'] = 'Os comentários que os utilizadores escrevem nos mapas são armazenados. Quando uma galeria materializa mapas de outra atividade, as cópias armazenadas podem também conter o trabalho dos estudantes e os seus nomes.';
$string['privacy:metadata:vimigallery_comment'] = 'Comentários que um utilizador publica nos mapas de uma galeria.';
$string['privacy:metadata:vimigallery_comment:content'] = 'O texto do comentário.';
$string['privacy:metadata:vimigallery_comment:timecreated'] = 'Quando o comentário foi publicado.';
$string['privacy:metadata:vimigallery_comment:userid'] = 'O utilizador que publicou o comentário.';
$string['privacy:metadata:vimigallery_item'] = 'Cópias de mapas materializados a partir de outra atividade, que podem conter o trabalho dos estudantes.';
$string['privacy:metadata:vimigallery_item:authorname'] = 'O nome apresentado para o autor do mapa.';
$string['privacy:metadata:vimigallery_item:mapjson'] = 'A cópia armazenada do mapa.';
$string['privacy:metadata:vimigallery_item:sourceuserid'] = 'O estudante de cujo trabalho a cópia deriva.';
$string['privacy:metadata:vimigallery_item_user'] = 'Que estudantes contribuíram para um mapa de grupo materializado.';
$string['privacy:metadata:vimigallery_item_user:userid'] = 'Um estudante que contribuiu para o mapa.';
$string['profile'] = 'Perfil';
$string['qtypesource'] = 'Teste';
$string['qtypesource_help'] = 'Escolha uma atividade Teste deste curso. As soluções modelo das suas perguntas ViMi Pad tornam-se os mapas desta galeria. As soluções modelo só são mostradas ao vivo a utilizadores que possam avaliar o teste.';
$string['refreshsnapshot'] = 'Atualizar a partir da origem';
$string['showauthors'] = 'Mostrar nomes dos autores';
$string['showauthors_help'] = 'Quando ativado, o nome do autor armazenado com cada mapa é mostrado por cima dele.';
$string['showtabs'] = 'Mostrar separadores mapa/lista';
$string['showtabs_help'] = 'Quando ativado, quem vê pode alternar cada mapa entre a vista gráfica e a vista de lista.';
$string['similarity'] = 'Semelhança: {$a}%';
$string['source_datafield'] = 'Um campo de atividade Base de dados';
$string['source_qtype'] = 'Uma atividade Teste (perguntas ViMi Pad)';
$string['source_upload'] = 'Ficheiros carregados';
$string['source_vimipad'] = 'Uma atividade ViMi Pad';
$string['sourcefiles'] = 'Ficheiros de mapas (JSON)';
$string['sourcefiles_help'] = 'Carregue um ou mais mapas ViMi Pad exportados como ficheiros JSON. Cada ficheiro torna-se um mapa na galeria.';
$string['sourceheader'] = 'Mapas';
$string['sourcemode'] = 'Mostrar';
$string['sourcemode_help'] = 'Referência mostra as soluções modelo das perguntas ViMi Pad do teste.';
$string['sourcemode_reference'] = 'Soluções modelo';
$string['sourcemode_submissions'] = 'Submissões dos estudantes';
$string['sourcetype'] = 'Origem dos mapas';
$string['sourcetype_help'] = 'Ficheiros carregados: um ou mais mapas JSON exportados. Um campo de atividade Base de dados: os valores ViMi Pad armazenados nas entradas de uma atividade Base de dados.';
$string['vimigallery:addinstance'] = 'Adicionar uma nova Galeria ViMi';
$string['vimigallery:manageitems'] = 'Curar os mapas de uma Galeria ViMi';
$string['vimigallery:view'] = 'Ver uma Galeria ViMi';
$string['vimipadsource'] = 'Atividade ViMi Pad';
$string['vimipadsource_help'] = 'Escolha uma atividade ViMi Pad deste curso. No modo submissões, os seus mapas submetidos tornam-se a galeria, seguindo as regras de acesso da atividade; no modo referência, a sua solução modelo é mostrada aos avaliadores.';
$string['visible'] = 'Visível';
