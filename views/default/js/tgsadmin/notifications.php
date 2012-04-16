<?php
/**
 * Elgg TGSAdmin externallinks JS
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2012
 * @link http://www.thinkglobalschool.com
 *
 */
?>
//<script>
elgg.provide('elgg.adminnotifications');

// Init 
elgg.adminnotifications.init = function() {
	// Delegate click handler for admin menu items
	$(document).delegate('.tgsadmin-notifications-admin-menu-item', 'click', elgg.adminnotifications.adminMenuClick);

	// Delegate click handler for enable script clicks
	$(document).delegate('.tgsadmin-enable-script', 'click', elgg.adminnotifications.enableScriptClick);
	
	// Delegate change handler for method checkbox change events
	$(document).delegate('.tgsadmin-user-notification-checkbox', 'change', elgg.adminnotifications.methodChange);
}

// Admin menu click handler
elgg.adminnotifications.adminMenuClick = function(event) {
	$('.tgsadmin-notifications-admin-menu-item').parent().removeClass('elgg-state-selected');
	$(this).parent().addClass('elgg-state-selected');

	$('.tgsadmin-notifications-menu-container').hide();
	
	$($(this).attr('href')).show();
	
	event.preventDefault();
}

// Click handler for enable all email notifications button
elgg.adminnotifications.enableScriptClick = function(event) {
	var type = $(this).attr('name');
	
	elgg.get(elgg.get_site_url() + "tgsadmin_notifications/enable" + type, {
		success: function(data) {
			$("#tgsadmin-notification-script-output").html(data);
		},
		error: function() {
			$("#tgsadmin-notification-script-output").html("There was an error loading output");
		}
	});
}

// Change handler for method checkbox change events
elgg.adminnotifications.methodChange = function(event) {
	var user_guid = $(this).attr('name');
	var method = $(this).attr('value');
	var checked = $(this).is(':checked');
	
	if (checked) {
		checked = 'yes';
	} else {
		checked = 'no';
	}

	elgg.action('tgsadmin/setnotification', {
		data: {
			user_guid: user_guid,
			method: method,
			checked: checked,
		},
		success: function(json) {
			// 
		}
	});

	event.preventDefault();
}

elgg.register_hook_handler('init', 'system', elgg.adminnotifications.init);