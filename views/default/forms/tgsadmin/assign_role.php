<?php
/**
 * Elgg TGSAdmin assign roles form
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com
 * 
 */

// Form labels/inputs
$params = array(
	'name' => 'role_guid',
	'show_hidden' => true,
);

$role_input = elgg_view('input/roledropdown', $params);

$groups_label = elgg_echo('tgsadmin:label:assigngroups');
$groups_input = elgg_view('input/dropdown', array(
	'id' => 'group_picker',
	'name' => 'group_guid',
	'options_values' => get_assign_groups()
));

$submit_button = elgg_view('input/submit', array('value' => elgg_echo('Submit')));
		
// Form content
$form_body = <<<HTML
	<label>Select Role</label><br /> 
	$role_input<br /><br />
	<label>$groups_label</label><br />
	$groups_input<br /><br />
	$submit_button
HTML;

echo $form_body;