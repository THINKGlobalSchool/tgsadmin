<?php
/**
 * Elgg TGSAdmin Unassign Role Action
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2016
 * @link http://www.thinkglobalschool.com
 *
 */

// get input
$role_guid	= get_input('remove_role_guid');
$group_guid = get_input('remove_group_guid');

// check values
if (empty($role_guid) || !$group_guid) {
	register_error(elgg_echo('tgsadmin:error:missing'));
	forward(REFERER);
}

// Get group object (need to check if its a group or a channel)
$group = get_entity($group_guid);
$role = get_entity($role_guid);

// Check for valid group/role
if (!elgg_instanceof($group, 'group') || !elgg_instanceof($role, 'object', 'role')) {
	register_error(elgg_echo('tgsadmin:error:invalidrolegroup'));
	forward(REFERER);
}

$users = roles_get_members($role_guid, 0);

// Set group as page owner to handle ACL's properly
elgg_set_page_owner_guid($group->guid);
elgg_set_ignore_access(TRUE);
elgg_set_page_owner_guid($group->guid);

foreach ($users as $user) {	
	$group->leave($user);
}

elgg_set_ignore_access(FALSE);
	
system_message(elgg_echo("tgsadmin:confirm:roleunassigned", array($group->name)));
forward(REFERER);
