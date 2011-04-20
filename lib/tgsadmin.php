<?php
/**
 * Elgg TGSAdmin lib
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2009-2010
 * @link http://www.thinkglobalschool.com
 *
 */

// Maintenance Mode uses this exception
class MaintenanceModeException extends Exception {}

/** Get admin page content **/
function assign_get_admin_content() {
	$content = elgg_view('assign/forms/assign_form');
	return $content;
}

/** 
 * Return an array suitable for a dropdown of 
 * all groups and, if enabled, channels
 * 
 * @return array
 */
function get_assign_groups() {
	$groups = elgg_get_entities(array('types' => 'group', 'limit' => 9999));
			
	if (elgg_is_active_plugin('shared_access')) {
	
		$channels = elgg_get_entities(array('types' => 'object',
											'subtypes' => 'shared_access',
											'limit' => 9999
									  		));
		$groups = array_merge($groups, $channels);
	}
	
	$dropdown = array();
	$dropdown[0] = 'Select..';

	foreach ($groups as $group) {
		$dropdown[$group->getGUID()] = $group->title ? "Channel: " . $group->title : "Group: " . $group->name;
	}
	
	return $dropdown;
}