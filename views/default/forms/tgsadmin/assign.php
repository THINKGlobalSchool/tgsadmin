<?php
/**
 * Elgg TGSAdmin assign form
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2012
 * @link http://www.thinkglobalschool.com
 * 
 */

// Form labels/inputs
$users_label = elgg_echo('tgsadmin:label:assignusers');
$users_input = elgg_view('input/userpicker', array(
												'id' => 'user_picker',
												'name' => 'users',
											));

$groups_label = elgg_echo('tgsadmin:label:assigngroups');
$groups_input = elgg_view('input/dropdown', array(
												'id' => 'group_picker',
												'name' => 'groups',
												'options_values' => get_assign_groups()
											));

$submit_button = elgg_view('input/submit', array('value' => elgg_echo('Submit')));
		
// Form content
$form_body = <<<HTML
	$title<br />
	<label>$users_label</label><br /> 
	$users_input<br /> 
	<label>$groups_label</label><br />
	$groups_input<br /><br />
	$submit_button
HTML;

echo $form_body;
