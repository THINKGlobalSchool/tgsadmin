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
 */

// get input
$users	= get_input('users');
$group_guid = get_input('groups');

// check values
if (empty($users) || !$group_guid) {
		register_error(elgg_echo('tgsadmin:error:missing'));
		forward($_SERVER['HTTP_REFERER']);
}

// Get group object (need to check if its a group or a channel)
$group = get_entity($group_guid);

foreach ($users as $user) {
	if (elgg_instanceof($group, 'group')) {
		// Set group as page owner to handle ACL's properly
		elgg_set_page_owner_guid($group->guid);
		elgg_set_ignore_access(TRUE);
		set_page_owner($group->guid);
		
		$user_entity = get_entity($user);
		
		$group->join($user_entity);
		elgg_set_ignore_access(FALSE);
	} else {
		// Channel
		add_entity_relationship($user, 'shared_access_member', $group_guid);
	}
}
	
system_message(elgg_echo("tgsadmin:confirm:assigned"));
forward($_SERVER['HTTP_REFERER']);
