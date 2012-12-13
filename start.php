<?php
/**
 * Elgg TGSAdmin start.php
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2012
 * @link http://www.thinkglobalschool.com
 * 
 * Provides the following features/functionality
 * - Autofriend: new users are automatically 'friended' with all of the other sites users
 * - Maintenance Mode: allows admins to take an elgg site down and display a maintenance message to regular users
 * - Assign: Allows admins to easily assign users to groups and channels
 * - External Links: Externallinks will open in a new tab/window
 * - Manage User Notifications
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
	
	tgsadmin_pqp_init();
	
	elgg_register_library('elgg:tgsadmin', elgg_get_plugins_path() . 'tgsadmin/lib/tgsadmin.php');
	elgg_load_library('elgg:tgsadmin');
	
	// Register handler for pagesetup event to set up admin menu
	elgg_register_event_handler('pagesetup', 'system', 'tgsadmin_setup_menu');
	
	
	/* ADMIN NOTIFICATIONS */
	// Register admin notifications JS
	$n_js = elgg_get_simplecache_url('js', 'tgsadmin/notifications');
	elgg_register_simplecache_view('js/tgsadmin/notifications');	
	elgg_register_js('elgg.adminnotifications', $n_js);
	
	elgg_register_page_handler('tgsadmin_notifications', 'tgsadmin_notifications_page_handler');
	
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
	
	/** OTHER UTILITES **/
	$tu_js = elgg_get_simplecache_url('js', 'tgsadmin/utilities');
	elgg_register_simplecache_view('js/tgsadmin/utilities');	
	elgg_register_js('elgg.tgsadmin_utilities', $tu_js);

	
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
	
	/* Extra HTMLawed Config */
	elgg_register_plugin_hook_handler('config', 'htmlawed', 'tgsadmin_htmlawed_config_handler');

	/* Customize outgoing emails */
	elgg_register_plugin_hook_handler('email', 'system', 'tgsadmin_email_handler');

	/* ACTIONS */	
	$action_base = elgg_get_plugins_path() . 'tgsadmin/actions/tgsadmin';
	elgg_register_action('tgsadmin/assign', "$action_base/assign.php", 'admin');
	elgg_register_action('tgsadmin/setnotification', "$action_base/setnotification.php", 'admin');
	elgg_register_action('tgsadmin/unassign', "$action_base/unassign.php", 'admin');
	elgg_register_action('tgsadmin/requestnewpassword', "$action_base/requestnewpassword.php", 'public');
	elgg_register_action('tgsadmin/move_entity', "$action_base/move_entity.php", 'admin');
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
		elgg_load_js('elgg.tgsadmin_utilities');
		
		// Assign users admin option
		elgg_register_admin_menu_item('administer', 'assign', 'users');
	
		// Manage user notifications
		elgg_register_admin_menu_item('administer', 'notifications', 'users');

		// Entity move utility
		elgg_register_admin_menu_item('administer', 'move_entity', 'administer_utilities');
		
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
 * Page handler for forgotten passwords
 *
 * @param array  $page_elements Page elements
 * @param string $handler The handler string
 *
 * @return void
 */
function tgsadmin_notifications_page_handler($page) {
	if (!elgg_is_admin_logged_in()) {
		forward();
	}

	switch($page[0]) {
		case 'enableemail':
			tgsadmin_enable_all_notifications_by_type('email');
			break;
		case 'enablesite':
			tgsadmin_enable_all_notifications_by_type('site');
			break;
	}
	
	return TRUE;
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

/**
 * Init PQP Profiler
 */
function tgsadmin_pqp_init() {
	/* PQP INIT */
	if (get_input('pqp_profiler') == 'pqp_profile') {
		global $CONFIG;
		include_once elgg_get_plugins_path() . "tgsadmin/vendors/pqp/classes/PhpQuickProfiler.php";
		$CONFIG->pqp_profiler = new PhpQuickProfiler(PhpQuickProfiler::getMicroTime(), elgg_get_plugins_path() . "tgsadmin/vendors/pqp/");
		$CONFIG->pqp_db = new stdClass();
		$CONFIG->pqp_db->queries = array();
		$CONFIG->pqp_db->queryCount = 0;
	}
	elgg_extend_view('page/elements/foot', 'tgsadmin/pqp');
}

/**
 * Add extra configuration for HTMLawed
 *
 * @param string $hook   Hook name
 * @param string $type   The type of hook
 * @param mixed  $config The HTMLawed config
 * @param array  $params Not used
 * @return mixed
 */
function tgsadmin_htmlawed_config_handler($hook, $type, $config, $params) {
	$config['cdata'] = 1;
	return $config;
}

/**
 * Customize outgoing emails
 *
 * @param string $hook   Hook name
 * @param string $type   The type of hook
 * @param mixed  $value  Return value
 * @param array  $params Mail params
 * @return mixed
 */
function tgsadmin_email_handler($hook, $type, $value, $params) {
	if (!elgg_in_context('elgg_send_email_passthrough')) {
		// Get site email address
		$site = get_entity(elgg_get_config('site_guid'));
		$site_email = $site->email;

		// Get no-reply email address (may have already fallen back to this)
		$noreply_email = 'noreply@' . get_site_domain($site->guid);

		// If we're sending an email from the site or noreply
		if ($params['from'] == $site_email || $params['from'] == $noreply_email) {
			// Force from address to noreply@
			if ($noreply = elgg_get_plugin_setting('noreplyemail', 'tgsadmin')) {
				$params['from'] = $noreply;
			} else {
				$params['from'] = $noreply_email;
			}

			// Append no reply message
			$params['body'] .= elgg_echo('tgsadmin:message:noreply');

			// Push passthrough context so we don't interrupt our custom email
			elgg_push_context('elgg_send_email_passthrough');

			// Send updated email
			elgg_send_email($params['from'], $params['to'], $params['subject'], $params['body'], $params['params']);

			return TRUE;
		}
	}
	// Carry on..
	return $value;
}
