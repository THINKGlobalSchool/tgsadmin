<?php
/**
 * Elgg TGSAdmin assign page
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2012
 * @link http://www.thinkglobalschool.com
 * 
 */
$form_vars = array('id' => 'assign-form');
$assign_users_form = elgg_view_form('tgsadmin/assign', $form_vars, array());
echo elgg_view_module('inline', elgg_echo('tgsadmin:label:assignuserstogroup'), $assign_users_form);

if (elgg_is_active_plugin('roles')) {
	$assign_roles_form =  elgg_view_form('tgsadmin/assign_role', $form_vars, array());
	echo elgg_view_module('inline', elgg_echo('tgsadmin:label:assignroletogroup'), $assign_roles_form);
}

$form_vars = array(
	'id' => 'group-select-form',
	'name' => 'group_select_form',
	'action' => elgg_get_site_url() . "admin/users/assign"
);
$unassign_form =  elgg_view_form('tgsadmin/groupselect', $form_vars, array());

if ($guid = get_input('entity_guid')) {
	$unassign_form .= elgg_view('assign/members', array('guid' => $guid));
}

echo elgg_view_module('inline', elgg_echo('tgsadmin:label:removeusersfromgroup'), $unassign_form);

if (elgg_is_active_plugin('roles')) {
	$form_vars = array('id' => 'unassign-form');
	$unassign_roles_form =  elgg_view_form('tgsadmin/unassign_role', $form_vars, array());
	echo elgg_view_module('inline', elgg_echo('tgsadmin:label:unassignroletogroup'), $unassign_roles_form);
}