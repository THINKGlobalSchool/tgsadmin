<?php
/**
 * Assign Unassign Users Action
 * 
 * @package Assign
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$user = get_entity(get_input('user'));
$entity = get_entity(get_input('e'));

// check values
if (!elgg_instanceof($user, 'user') || !(elgg_instanceof($entity, 'group') || elgg_instanceof($entity, 'object', 'shared_access'))) {
		register_error(elgg_echo('tgsadmin:error:invalidusergroup'));
		forward(REFERER);
}

if (elgg_instanceof($entity, 'group')) {
	// Set group as page owner to handle ACL's properly
	elgg_set_page_owner_guid($entity->guid);
	// Leave group
	leave_group($entity->getGUID(), $user->getGUID());
} else if (elgg_instanceof($entity, 'object', 'shared_access')) {
	// Leave SAV
	remove_entity_relationship($user->getGUID(), 'shared_access_member', $entity->getGUID());
}

system_message(elgg_echo("tgsadmin:confirm:unassigned"));
forward('admin/users/assign?entity_guid=' . $entity->getGUID());
