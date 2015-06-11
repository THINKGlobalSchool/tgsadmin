<?php
/**
 * Elgg TGSAdmin admin notifications page
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2012
 * @link http://www.thinkglobalschool.com
 * 
 */
?>
<style type='text/css'>
.elgg-menu-filter {
	margin-bottom: 5px;
	border-bottom: 2px solid #ccc;
	display: table;
	width: 100%;
}
.elgg-menu-filter > li {
	float: left;
	border: 2px solid #ccc;
	border-bottom: 0;
	background: #eee;
	margin: 0 0 0 10px;
	
	-webkit-border-radius: 5px 5px 0 0;
	-moz-border-radius: 5px 5px 0 0;
	border-radius: 5px 5px 0 0;
}
.elgg-menu-filter > li:hover {
	background: #dedede;
}
.elgg-menu-filter > li > a {
	text-decoration: none;
	display: block;
	padding: 3px 10px 0;
	text-align: center;
	height: 21px;
	color: #999;
}
.elgg-menu-filter > li > a:hover {
	background: #dedede;
	color: #4690D6;
}
.elgg-menu-filter > .elgg-state-selected {
	border-color: #ccc;
	background: white;
}
.elgg-menu-filter > .elgg-state-selected > a {
	position: relative;
	top: 2px;
	background: white;
}
</style>
<?php
elgg_load_js('elgg.adminnotifications');

// Simple tab interface for switching between feed lookup and manual entry
elgg_register_menu_item('tgsadmin-notifications-admin-menu', array(
	'name' => 'notification_users',
	'text' => elgg_echo('tgsadmin:label:usernotifications'),
	'href' => '#tgsadmin-admin-user-notifications',
	'priority' => 0,
	'item_class' => 'elgg-state-selected',
	'link_class' => 'tgsadmin-notifications-admin-menu-item',
));

elgg_register_menu_item('tgsadmin-notifications-admin-menu', array(
	'name' => 'notification_scripts',
	'text' => elgg_echo('tgsadmin:label:notificationsscripts'),
	'href' => '#tgsadmin-admin-scripts',
	'priority' => 1,
	'link_class' => 'tgsadmin-notifications-admin-menu-item',
));

$menu = elgg_view_menu('tgsadmin-notifications-admin-menu', array(
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz elgg-menu-filter elgg-menu-filter-default'
));

$enable_email_input = elgg_view('input/button', array(
	'value' => elgg_echo('tgsadmin:label:enableemail'),
	'class' => 'tgsadmin-enable-script',
	'name' => 'email',
));

$enable_site_input = elgg_view('input/button', array(
	'value' => elgg_echo('tgsadmin:label:enablesite'),
	'class' => 'tgsadmin-enable-script',
	'name' => 'site',
));

$user_notifications = elgg_view_form('tgsadmin/usernotifications');

$content = <<<HTML
	<div>
		$menu
	</div>
	<div id='tgsadmin-admin-user-notifications' class='tgsadmin-notifications-menu-container'>
		$user_notifications
	</div>
	<div style='display: none;' id='tgsadmin-admin-scripts' class='tgsadmin-notifications-menu-container'>
		$enable_email_input
		$enable_site_input
		<div id='tgsadmin-notification-script-output'>
		</div>
	</div>
HTML;
echo $content;