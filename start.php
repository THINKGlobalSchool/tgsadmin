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
 * - Assign: Allows admins to easily assign users to groups and channels
 * - External Links: Externallinks will open in a new tab/window
 * 
 * Also includes the following tweaks from the tgstweaks plugin
 * - Extend river wrapper to show which access level/group each entry belongs to
 * - Extend messages view to add next/previous buttons
 * - Better looking tag cloud
 * - Add groups to activity sidebar
 * 
 * The following views have been added/overidden
 * - river/object/file/create (to show old files entries)
 * - output/tagcloud                (tags point to a tagdashboard/better formatting)
 * - output/tags                    (tags point to a tagdashboard)
 * - output/tag                     (tags point to a tagdashboard)
 * - core/settings/account/email    (email is read only for non-admins)
 * - core/settings/account/password (added a link to reset current password)
 */

// Register init event 
elgg_register_event_handler('init', 'system', 'tgsadmin_init');

// Registering for the 'ready' system event for externallinks
elgg_register_event_handler('ready', 'system', 'tgsadmin_externallinks_init');

/**
 * TGSAdmin Init
 */
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
	
	// Unregister forgotpassword page handler
	elgg_unregister_page_handler('forgotpassword');
	
	// Register new page hanler 
	elgg_register_page_handler('forgotpassword', 'tgsadmin_forgotpassword_page_handler');
	
	// Unregister resetpassword page handler
	elgg_unregister_page_handler('resetpassword');

	// Register new page handler
	elgg_register_page_handler('resetpassword', 'tgsadmin_resetpassword_page_handler');
	
	/* TGS TWEAKS */
	// Include the access level in the river item view
	elgg_extend_view('css/elgg', 'tweaks/css');
	
	// Override CSS
	elgg_extend_view('css/elgg', 'css/tgsadmin/overrides');
	
	// Extend river item view
	elgg_extend_view('river/elements/layout', 'tweaks/access_display', 501);
	
	// Include the messages navigation 
	elgg_extend_view('object/messages', 'tweaks/messages_navigation');
	
	// Extend Sidebar for tag cloud
	elgg_extend_view('page/elements/sidebar', 'tweaks/tagcloud', 502);
	
	/* Assign/Unassign */
	elgg_extend_view('css/admin', 'css/tgsadmin/assign');

	/* ACTIONS */	
	$action_base = elgg_get_plugins_path() . 'tgsadmin/actions/tgsadmin';
	elgg_register_action('tgsadmin/assign', "$action_base/assign.php", 'admin');
	elgg_register_action('tgsadmin/unassign', "$action_base/unassign.php", 'admin');
	elgg_register_action('tgsadmin/requestnewpassword', "$action_base/requestnewpassword.php", 'public');
}

/**
 * External links init
 * @return bool
 */
function tgsadmin_externallinks_init() {	
	if (elgg_get_plugin_setting('enable_externallinks', 'tgsadmin') == 'yes') {
		$js = elgg_get_simplecache_url('js', 'tgsadmin/externallinks');
		elgg_register_simplecache_view('js/tgsadmin/externallinks');	
		elgg_register_js('elgg.externallinks', $js);
		elgg_load_js('elgg.externallinks');
	}
	
	return true;
}

/**
 * Autofriend event 
 */
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

/**
 * TGSAdmin pagesetup
 */
function tgsadmin_setup_menu() {
	if (elgg_in_context('admin')) {
		elgg_register_admin_menu_item('administer', 'assign', 'users');
		// Not using this yet
		//elgg_register_admin_menu_item('administer', 'settings', 'tgsadmin');
	}
}

/**
 * Page handler for forgotten passwords
 *
 * @param array  $page_elements Page elements
 * @param string $handler The handler string
 *
 * @return void
 */
function tgsadmin_forgotpassword_page_handler($page_elements, $handler) {
	if (elgg_is_logged_in()) {
		forward();
	}

	$title = elgg_echo("user:password:lost");
	$content = elgg_view_title($title);

	$content .= elgg_view_form('tgsadmin/requestnewpassword', array(
		'class' => 'elgg-form-account',
	));

	$body = elgg_view_layout("one_column", array('content' => $content));
	echo elgg_view_page($title, $body);
}

/**
 * Page handler for resetting passwords
 *
 * @param array  $page_elements Page elements
 * @param string $handler The handler string
 *
 * @return void
 */
function tgsadmin_resetpassword_page_handler($page_elements, $handler) {
	if (elgg_is_logged_in()) {
		// Allow logged in users to reset their password
		$user = elgg_get_logged_in_user_entity();
		$user_guid = $user->guid;
		
		// Need to generate a code (skipping email validation)
		$code = generate_random_cleartext_password();
		$user->setPrivateSetting('passwd_conf_code', $code);
	
	} else {
		$user_guid = get_input('u');
		$code = get_input('c');

		$user = get_entity($user_guid);
	}

	// don't check code here to avoid automated attacks
	if (!$user instanceof ElggUser) {
		register_error(elgg_echo('user:passwordreset:unknown_user'));
		forward();
	}

	$params = array(
		'guid' => $user_guid,
		'code' => $code,
	);
	$form = elgg_view_form('user/passwordreset', array('class' => 'elgg-form-account'), $params);

	$title = elgg_echo('resetpassword');
	$content = elgg_view_title(elgg_echo('resetpassword')) . $form;

	$body = elgg_view_layout('one_column', array('content' => $content));

	echo elgg_view_page($title, $body);

}
