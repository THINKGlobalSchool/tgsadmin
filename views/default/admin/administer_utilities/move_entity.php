<?php 
/**
 * Elgg TGSAdmin move entities admin utility
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2012
 * @link http://www.thinkglobalschool.com
 *
 */
elgg_load_js('elgg.tgsadmin_utilities');

$move_label = elgg_echo('tgsadmin:label:move');
$move_content = elgg_view_form('tgsadmin/move_entity');

$move_module = elgg_view_module('inline', $move_label, $move_content);

echo $move_module;
echo "<div id='tgsadmin-utility-output'></div>";
