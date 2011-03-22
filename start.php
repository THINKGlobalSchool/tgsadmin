<?php
/**
 * Elgg TGSAdmin start.php
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2009-2010
 * @link http://www.thinkglobalschool.com
 * 
 * Provides the following features/functionality
 * - Autofriend: new users are automatically 'friended' with all of the other sites users
 * - Maintenance Mode: allows admins to take an elgg site down and display a maintenance message to regular users
 *
 */

// Register init event 
elgg_register_event_handler('init', 'system', 'tgsadmin_init');

/* TGSAdmin Init */
function tgsadmin_init() {
	
	elgg_register_library('elgg:tgsadmin', elgg_get_plugins_path() . 'tgsadmin/lib/tgsadmin.php');
	elgg_load_library('elgg:tgsadmin');
	
	
	// Register handler for pagesetup event to set up admin menu
	elgg_register_event_handler('pagesetup', 'system', 'tgsadmin_setup_menu');
	
	/* AUTOFRIEND */
	// Register an event handler to catch the creation of new users
	elgg_register_event_handler('create', 'user', 'autofriend_event',501);
	
	/* MAINTENANCE MODE */
	
	// If logged in user isn't an admin, forward to mainenance page
	if (elgg_get_plugin_setting('enable_maintenance', 'tgsadmin') == 'yes') {
		if (!elgg_is_admin_logged_in() && elgg_is_logged_in()) {
			global $CONFIG;
			$CONFIG->pagesetupdone = true;

			elgg_set_viewtype('failsafe');
			$body = elgg_view("messages/exceptions/maintenanceexception", array(
				// Throw custom exception
				'object' => new MaintenanceModeException(elgg_get_plugin_setting('maintenance_message', 'tgsadmin'))
			));
			echo elgg_view_page(elgg_get_plugin_setting('maintenance_title', 'tgsadmin'), $body);
			exit;
		}
	}
	
}

/* Autofriend event */ 
function autofriend_event($event, $object_type, $object) {	
	// Only if this is enabled
	if (elgg_get_plugin_setting('enable_autofriend', 'tgsadmin') == 'yes') {
		// Get site members
		$site = get_entity($object['site_guid']);
		$members = $site->getMembers(array('limit' => 0));
		if (($members) && is_array($members)) {
			foreach ($members as $member) {
				if ($object instanceof ElggUser) {
					// Add newly created user to each members friends
					$member->addFriend($object->getGUID());
					// Add member to new user's friends 
					$object->addFriend($member->getGUID());
				}
			}		
		}
	}
}

/* TGSAdmin pagesetup */
function tgsadmin_setup_menu() {
	if (elgg_in_context('admin')) {
		// Not using this yet
		//elgg_register_admin_menu_item('administer', 'settings', 'tgsadmin');
	}
}
