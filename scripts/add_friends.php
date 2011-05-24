<?php

/** Add all site users as friend */
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
global $CONFIG;
admin_gatekeeper();

$username = get_input('u');

$user = get_user_by_username($username);

// Clear the notification hook
elgg_unregister_event_handler('create', 'friend', 'relationship_notification_hook');

if ($user) {
	global $CONFIG;

	$site = get_entity($CONFIG->site_id);

	$members = $site->getMembers(array('limit' => 0));

	if (($members) && is_array($members)) {
		foreach ($members as $member) {
			// Add member to new user's friends 
			echo "Adding: {$member->name}<br />";
			echo "Result: " . $user->addFriend($member->getGUID()) . "<br />";
		}		
	}
} else {
	echo 'Invalid User';
}

