<?php
/**
 * Elgg TGSAdmin move entity form
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2012
 * @link http://www.thinkglobalschool.com
 *
 */

$entity_label = elgg_echo('tgsadmin:label:entityguid');
$entity_input = elgg_view('input/text', array(
	'name' => 'entity_guid',
));

$group_label = elgg_echo('tgsadmin:label:groupguid');
$group_input = elgg_view('input/text', array(
	'name' => 'group_guid',
));

$submit_input = elgg_view('input/submit', array(
	'name' => 'entity_move',
	'class' => 'elgg-button elgg-button-action tgsadmin-move-button',
	'value' => elgg_echo('tgsadmin:label:move'),
));

$content = <<<HTML
	<div>
		<label>$entity_label</label>
		$entity_input
	</div>
	<div>
		<label>$group_label</label>
		$group_input
	</div>
	<div>
		$submit_input
	</div>
HTML;

echo $content;