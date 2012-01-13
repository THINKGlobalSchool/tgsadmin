<?php
/**
 * Elgg TGSAdmin assign page
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2009-2010
 * @link http://www.thinkglobalschool.com
 * 
 */
$form_vars = array('id' => 'assign-form');
echo elgg_view_form('tgsadmin/assign', $form_vars, array());

$form_vars = array(
	'id' => 'group-select-form',
	'name' => 'group_select_form',
	'action' => elgg_get_site_url() . "admin/users/assign"
);
echo elgg_view_form('tgsadmin/groupselect', $form_vars, array());

if ($guid = get_input('entity_guid')) {
	echo elgg_view('assign/members', array('guid' => $guid));
}
return $content;