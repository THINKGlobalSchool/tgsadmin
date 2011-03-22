<?php
/**
 * Elgg TGSAdmin start.php
 *
 * @package ElggAutoFriend
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2009-2010
 * @link http://www.thinkglobalschool.com
 * 
 * Provides the following features/functionality
 * - Autofriend, new users are automatically 'friended' with all of the other sites users
 *
 */

// Register init event 
elgg_register_event_handler('init', 'system', 'tgsadmin_init');

/* TGSAdmin Init */
function tgsadmin_init() {
	
	// Register handler for pagesetup event to set up admin menu
	elgg_register_event_handler('pagesetup', 'system', 'tgsadmin_setup_menu');
	
	/* AUTOFRIEND */
	
	// Register an event handler to catch the creation of new users
	elgg_register_event_handler('create', 'user', 'autofriend_event',501);
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
