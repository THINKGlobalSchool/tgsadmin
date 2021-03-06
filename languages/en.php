<?php
/**
 * Elgg TGSAdmin english translation
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2012
 * @link http://www.thinkglobalschool.com
 *
 */
$english = array(
	
	// Admin menu entries
	'admin:tgsadmin' => 'TGS Admin',
	'admin:tgsadmin:settings' => 'Settings',
	'admin:users:assign' => 'Assign Users',
	'admin:users:notifications' => 'Notifications',
	'admin:plugins:category:tgs' => 'TGS',
	'admin:administer_utilities:move_entity' => 'Move Entity',
	
	// Labels
	'tgsadmin:label:enableautofriend' => 'Enable autofriend',
	'tgsadmin:label:enablemaintenance' => 'Enable maintenance mode',
	'tgsadmin:label:enableexternallinks' => 'Enable external links (open external links in new window)',
	'tgsadmin:label:enableexecution' => 'Enable execution time logging',
	'tgsadmin:label:mmtitle' => 'Maintenance Title',
	'tgsadmin:label:mmmessage' => 'Maintenance Message',
	'tgsadmin:label:assignusers' => 'Select Users',
	'tgsadmin:label:assigngroups' => 'Assign To',
	'tgsadmin:label:unassign' => 'Unassign Users',
	'tgsadmin:label:selectgroup' => 'Select Group',
	'tgsadmin:label:remove' => 'Remove',
	'tgsadmin:label:settings:autofriend' => 'Autofriend Settings',
	'tgsadmin:label:settings:externalsettings' => 'External Links Settings',
	'tgsadmin:label:settings:maintenancesettings' => 'Maintenance Mode Settings',
	'tgsadmin:label:editusersettings' => 'Edit settings',
	'tgsadmin:label:usernameemail' => 'Username -or- Email',
	'tgsadmin:password:text' => 'To request a new password, enter your username or email below and click the Request button.',
	'tgsadmin:label:forgotcurrent' => 'I forget my current password',
	'tgsadmin:label:usernotifications' => 'User Notification Settings',
	'tgsadmin:label:notificationsscripts' => 'Scripts',
	'tgsadmin:label:enableemail' => 'Enable Email Notifications',
	'tgsadmin:label:enablesite' => 'Enable Site Notifications',
	'tgsadmin:label:move' => 'Move Entity',
	'tgsadmin:label:entityguid' => 'Entity GUID',
	'tgsadmin:label:groupguid' => 'Group GUID',
	'tgsadmin:label:emailsettings' => 'Email Settings',
	'tgsadmin:label:loggingsettings' => 'Logging Settings',
	'tgsadmin:label:noreplyemail' => 'No reply email',
	'tgsadmin:label:assignuserstogroup' => 'Assign Individual Users To Group',
	'tgsadmin:label:assignroletogroup' => 'Assign Role To Group',
	'tgsadmin:label:removeusersfromgroup' => 'Assign Individual Users To Group',

	'sidebar:groups:createorjoinlink' => 'Create or Join a Group', 

	// Confirmation
	'tgsadmin:confirm:assigned' => 'Users successfully assigned to %s',
	'tgsadmin:confirm:unassigned' => 'User successfully unassigned',
	
	// Error 
	'tgsadmin:error:missing' => 'Required fields missing: Need at least one user',
	'tgsadmin:error:invalidusergroup' => 'Invalid user or group',
	'tgsadmin:error:invalidrolegroup' => 'Invalid role or group',
	'tgsadmin:error:invaliduser' => 'Invalid User',
	'tgsadmin:error:nodata' => 'No Data',
	'tgsadmin:error:usernotification' => 'There was an error saving the user notification setting',
	'tgsadmin:success:usernotification' => 'Successfully saved user notification setting ',

	// No reply message
	'tgsadmin:message:noreply' => "\n\n*** This mailbox is unattended. Please do not reply to this message. ***",

	// Core email notification overrides
	'friend:newfriend:body' => "%s has made you a friend!

To view their profile, click here:

%s",

	'generic_comment:email:body' => "You have a new comment on your item \"%s\" from %s. It reads:


%s


To reply or view the original item, click here:

%s

To view %s's profile, click here:

%s",

	'messageboard:email:body' => "You have a new message board comment from %s. It reads:


%s


To view your message board comments, click here:

%s

To view %s's profile, click here:

%s",

	'messages:email:body' => "You have a new message from %s. It reads:

%s


To view your messages, click here:

%s

To send %s a message, click here:

%s",

	'profile:comment:body' => "You have a new message on your comment wall from %s. It reads:


%s


To view your message board comments, click here:

%s

To view %s's profile, click here:

%s",

);

add_translation('en',$english);
