<?php
// Purge tidypics albums older than given date
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
global $CONFIG;
admin_gatekeeper();

$check = get_input('check', NULL);

echo "<pre>";

if ($check) {
	
	$album = get_entity($check);
	
	if (elgg_instanceof($album, 'object', 'album')) {
		echo "ALBUM COVER
";

		$cover_guid = $album->cover;
		echo "IMAGE GUID: {$cover_guid}
";
		
		$image = get_entity($cover_guid);
		
		var_dump($image);
	}
	
}

echo "</pre>";