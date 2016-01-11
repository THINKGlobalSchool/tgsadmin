<?php
/**
 * Elgg TGSAdmin start.php
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2016
 * @link http://www.thinkglobalschool.org
 * 
 * Provides the following features/functionality
 * - Assign: Allows admins to easily assign users to groups
 * - External Links: Externallinks will open in a new tab/window
 * - Manage User Notifications
 * 
 * Also includes the following tweaks from the tgstweaks plugin
 * - Extend river wrapper to show which access level/group each entry belongs to
 * - Extend messages view to add next/previous buttons
 * - Add groups to activity sidebar
 * 
 * The following views have been added/overidden
 * - river/object/file/create (to show old files entries)
 * - output/tag                     (tags point to a tagdashboard)
 * - core/settings/account/email    (email is read only for non-admins)
 * - core/settings/account/password (added a link to reset current password)
 *
 * Other features:
 * - Secure cron
 */

// Register init event 
elgg_register_event_handler('init', 'system', 'tgsadmin_init');

// Registering for the 'ready' system event for externallinks
elgg_register_event_handler('ready', 'system', 'tgsadmin_externallinks_init');

// Include extra logic when handling exceptions
elgg_set_config('exception_include', elgg_get_plugins_path() . 'tgsadmin/lib/exception.php');

/**
 * TGSAdmin Init
 */
function tgsadmin_init() {	
	elgg_register_library('elgg:tgsadmin', elgg_get_plugins_path() . 'tgsadmin/lib/tgsadmin.php');
	elgg_load_library('elgg:tgsadmin');

	// Override old htmlawed library
	elgg_register_library('htmlawed', elgg_get_plugins_path() . 'tgsadmin/lib/htmlawed.php');
	
	// Register handler for pagesetup event to set up admin menu
	elgg_register_event_handler('pagesetup', 'system', 'tgsadmin_setup_menu');
	
	// Disable system logging
	elgg_unregister_event_handler('log', 'systemlog', 'system_log_default_logger');
	elgg_unregister_event_handler('all', 'all', 'system_log_listener');
	
	/* ADMIN NOTIFICATIONS */
	// Register admin notifications JS
	$n_js = elgg_get_simplecache_url('js', 'tgsadmin/notifications');
	elgg_register_simplecache_view('js/tgsadmin/notifications');	
	elgg_register_js('elgg.adminnotifications', $n_js);
	
	elgg_register_page_handler('tgsadmin_notifications', 'tgsadmin_notifications_page_handler');
	
	/** OTHER UTILITES **/
	$tu_js = elgg_get_simplecache_url('js', 'tgsadmin/utilities');
	elgg_register_simplecache_view('js/tgsadmin/utilities');	
	elgg_register_js('elgg.tgsadmin_utilities', $tu_js);

	
	// Unregister forgotpassword page handler
	elgg_unregister_page_handler('forgotpassword');
	
	// Register new page hanler 
	elgg_register_page_handler('forgotpassword', 'tgsadmin_forgotpassword_page_handler');
	
	// Unregister resetpassword/changepassword page handler
	elgg_unregister_page_handler('forgotpassword');
	elgg_unregister_page_handler('changepassword');

	// Register new page handlers
	elgg_register_page_handler('forgotpassword', 'tgsadmin_resetpassword_page_handler');
	elgg_register_page_handler('changepassword', 'tgsadmin_changepassword_page_handler');

	/* Secure CRON */
	elgg_unregister_page_handler('cron');

	elgg_register_page_handler('cron', 'tgsadmin_cron_page_handler');
	
	/* TGS TWEAKS */
	// Include the access level in the river item view
	elgg_extend_view('css/elgg', 'tweaks/css');
	
	// Override CSS
	elgg_extend_view('css/elgg', 'css/tgsadmin/overrides');
	
	// Extend river item view
	elgg_extend_view('river/elements/layout', 'tweaks/access_display', 501);
	
	// Include the messages navigation 
	elgg_extend_view('object/messages', 'tweaks/messages_navigation');
	
	/* Assign/Unassign */
	elgg_extend_view('css/admin', 'css/tgsadmin/assign');
	
	/* Extra HTMLawed Config */
	elgg_register_plugin_hook_handler('config', 'htmlawed', 'tgsadmin_htmlawed_config_handler');
	elgg_register_plugin_hook_handler('allowed_styles', 'htmlawed', 'tgsadmin_htmlawed_allowed_styles_handler');

	/* Customize outgoing emails */
	elgg_register_plugin_hook_handler('email', 'system', 'tgsadmin_email_handler');

	/* Re-register blog notification handler */
	elgg_unregister_plugin_hook_handler('notify:entity:message', 'object', 'blog_notify_message');
	elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'tgsadmin_blog_notify_message');

	/* Admin stats */
	if (elgg_get_plugin_setting('enable_execution_logging', 'tgsadmin') == 'yes') {
		// Extend topbar
		elgg_extend_view('page/elements/foot', 'tgsadmin/admin_stats', 1);

		elgg_extend_view('page/elements/topbar', 'tgsadmin/topbar_ready', 99999);

		/* Ready system handling (for profiling system boot time) */
		//elgg_register_event_handler('ready', 'system', 'tgsadmin_system_ready_handler',9999);

		/* Register a shutdown function to log execution time */
		//register_shutdown_function('tgsadmin_shutdown');
	}

	/* Externallinks */
	$js = elgg_get_simplecache_url('js', 'tgsadmin/externallinks');
	elgg_register_simplecache_view('js/tgsadmin/externallinks');	
	elgg_register_js('elgg.externallinks', $js);

	/* ACTIONS */	
	$action_base = elgg_get_plugins_path() . 'tgsadmin/actions/tgsadmin';
	elgg_register_action('tgsadmin/assign', "$action_base/assign.php", 'admin');
	elgg_register_action('tgsadmin/assign_role', "$action_base/assign_role.php", 'admin');
	elgg_register_action('tgsadmin/setnotification', "$action_base/setnotification.php", 'admin');
	elgg_register_action('tgsadmin/unassign', "$action_base/unassign.php", 'admin');
	elgg_register_action('tgsadmin/requestnewpassword', "$action_base/requestnewpassword.php", 'public');
	elgg_register_action('tgsadmin/move_entity', "$action_base/move_entity.php", 'admin');
}

/**
 * Cron handler
 *
 * @param array $page Pages
 *
 * @return bool
 * @throws CronException
 * @access private
 */
function tgsadmin_cron_page_handler($page) {
	if (!isset($page[0])) {
		forward();
	}

	// Secure cron with a simple query string key
	$key = elgg_get_plugin_setting('cronquerykey', 'tgsadmin');

	if (get_input('key') !== $key) {
		return false;
	}

	$period = strtolower($page[0]);

	$allowed_periods = elgg_get_config('elgg_cron_periods');

	if (!in_array($period, $allowed_periods)) {
		throw new \CronException("$period is not a recognized cron period.");
	}

	// Get a list of parameters
	$params = array();
	$params['time'] = time();

	// Data to return to
	$old_stdout = "";
	ob_start();

	$old_stdout = elgg_trigger_plugin_hook('cron', $period, $params, $old_stdout);
	$std_out = ob_get_clean();

	echo $std_out . $old_stdout;

	return true;
}

/**
 * External links init
 * @return bool
 */
function tgsadmin_externallinks_init() {	
	if (elgg_get_plugin_setting('enable_externallinks', 'tgsadmin') == 'yes') {
		elgg_load_js('elgg.externallinks');
	}
	
	return true;
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
	$title = elgg_echo("user:password:lost");

	$content = elgg_view_form('tgsadmin/requestnewpassword', array(
		'class' => 'elgg-form-account',
	));

	if (elgg_get_config('walled_garden')) {
		elgg_load_css('elgg.walled_garden');
		$body = elgg_view_layout('walled_garden', array('content' => $content));
		echo elgg_view_page($title, $body, 'walled_garden');
	} else {
		$body = elgg_view_layout('one_column', array(
			'title' => $title, 
			'content' => $content,
		));
		echo elgg_view_page($title, $body);
	}	
}

function tgsadmin_changepassword_page_handler($page_elements, $handler) {
	$user_guid = get_input('u');
	$code = get_input('c');

	$user = get_entity($user_guid);

	// don't check code here to avoid automated attacks
	if (!$user instanceof ElggUser) {
		register_error(elgg_echo('user:resetpassword:unknown_user'));
		forward();
	}

	$title = elgg_echo('changepassword');

	$params = array(
		'guid' => $user_guid,
		'code' => $code,
	);
	$content = elgg_view_form('user/changepassword', array('class' => 'elgg-form-account'), $params);

	$body = elgg_view_layout('one_column', array(
		'title' => $title,
		'content' => $content,
	));
	echo elgg_view_page($title, $body);
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
	$config['elements'] = "*+section";
	$config['deny_attribute'] = 'on*';
	return $config;
}

/**
 * Allow additional styles for HTMLawed
 *
 * @param string $hook   Hook name
 * @param string $type   The type of hook
 * @param mixed  $config The HTMLawed config
 * @param array  $params Not used
 * @return mixed
 */
function tgsadmin_htmlawed_allowed_styles_handler($hook, $type, $config, $params) {
	$allowed_styles[] = 'width';
	$allowed_styles[] = 'height';
	$allowed_styles[] = 'text-align';
	$allowed_styles[] = 'padding';
	$allowed_styles[] = 'padding-top';
	$allowed_styles[] = 'padding-left';
	$allowed_styles[] = 'padding-right';
	$allowed_styles[] = 'padding-bottom';
	$allowed_styles[] = 'margin';
	$allowed_styles[] = 'margin-left';
	$allowed_styles[] = 'margin-right';
	$allowed_styles[] = 'margin-top';
	$allowed_styles[] = 'margin-bottom';
	$allowed_styles[] = 'float';
	$allowed_styles[] = 'font-size';
	$allowed_styles[] = 'background-color';
	$allowed_styles[] = 'color';
	return $allowed_styles;
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
		$noreply_email = 'noreply@' . $site->getDomain();

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

/**
 * Set the blog notification message body
 * 
 * @param string $hook    Hook name
 * @param string $type    Hook type
 * @param string $message The current message body
 * @param array  $params  Parameters about the blog posted
 * @return string
 */
function tgsadmin_blog_notify_message($hook, $type, $message, $params) {
	$entity = $params['entity'];
	$to_entity = $params['to_entity'];
	$method = $params['method'];
	if (elgg_instanceof($entity, 'object', 'blog')) {
		$descr = elgg_get_excerpt($entity->description); // Send excerpt from desc
		$title = $entity->title;
		$owner = $entity->getOwnerEntity();
		return elgg_echo('blog:notification', array(
			$owner->name,
			$title,
			$descr,
			$entity->getURL()
		));
	}
	return null;
}


function tgsadmin_get_execution_time() {
	global $START_MICROTIME;
	return round((microtime(true) - $START_MICROTIME), 4);
}

// Shutdown handler intended to catch fatal errors and send an email notification
function fatalErrorShutdownHandler() {
  	$last_error = error_get_last();

  	$notify = elgg_get_plugin_setting('fatalemail', 'tgsadmin');

  	$type = $last_error['type'];

	if (($type === E_ERROR) && !empty($notify)) { 
		// Send mail
		$current_url = current_page_url();
		$time = time();
		mail($notify, "Fatal error: {$time}", "{$last_error['type']} {$last_error['message']} In File: {$last_error['file']} Line: {$last_error['line']} On page: {$current_url}");
	}
}

register_shutdown_function('fatalErrorShutdownHandler');
