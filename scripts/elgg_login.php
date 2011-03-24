<?php

	/** Script to remove a users google apps connection **/
	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	global $CONFIG;
	admin_gatekeeper();

	$username = get_input('u');
	
	login(get_user_by_username($username));
	forward('index.php');

?>