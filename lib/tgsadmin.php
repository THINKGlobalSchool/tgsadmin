<?php
/**
 * Elgg TGSAdmin lib
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2012
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

/**
 * Ajax die, helper function that registers and error and calls forward. 
 * This is only really useful for testing ajax actions
 */
function ajaxdie($msg) {
	register_error("AJAX DIE: $msg");
	forward(REFERER);
}

/** Notifications scripts **/

/**
 * Enable all notifications with given type
 *
 */
function tgsadmin_enable_all_notifications_by_type($notification_type) {
	if (!elgg_is_admin_logged_in()) {
		return FALSE; // Get out if not admin
	}

	$options = array(
		'type' => 'user',
		'limit' => 0,
	);

	$users = new ElggBatch('elgg_get_entities', $options);

	echo "<pre>";

	echo "ENABLE USER '{$notification_type}' NOTIFICATIONS <br /><br />";
 "</form>";	

	foreach ($users as $user) {
		echo "\r\n{$user->name}\r\n";
		set_user_notification_setting($user->guid, $notification_type, TRUE);
		$user_notifications = get_user_notification_settings($user->guid);
		
		foreach ($user_notifications as $type => $setting) {
			echo "	{$type}: {$setting}\r\n";
		}
	}
	echo "</pre>";
	
	return TRUE;
}
