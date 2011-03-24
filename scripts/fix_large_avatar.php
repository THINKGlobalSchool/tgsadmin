<?php

	/** Script to fix a users large profile avatar **/
	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	global $CONFIG;
	admin_gatekeeper();

	$members = elgg_get_entities(array(
		'type' => 'user',
		'limit' => 0,
	));

	foreach($members as $member) {
		var_dump($member->username);
						
			$filehandler = new ElggFile();
			$filehandler->owner_guid = $member->getGUID();
			$filehandler->setFilename("profile/" . $member->getGUID() . "master" . ".jpg");
			$filename = $filehandler->getFilenameOnFilestore();
			
			
			$large = get_resized_image_from_existing_file($filename, 200, 200, true, $member->x1, $member->y1, $member->x2, $member->y2);
			
			$filehandler = new ElggFile();
			$filehandler->owner_guid = $member->getGUID();
			$filehandler->setFilename("profile/" .  $member->getGUID() . "large.jpg");
			$filehandler->open("write");
			$filehandler->write($large);
			$filehandler->close();
	}

?>