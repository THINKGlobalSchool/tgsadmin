<?php
	/** Validate a user account **/
	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	global $CONFIG;
	admin_gatekeeper();

	$username = get_input('u');
	
	$user = get_user_by_username($username);

	echo "User validated: " . $user->validated;
	
	elgg_set_user_validation_status($user->getGUID(), TRUE, 'admin_created');
	$user->save();
?>