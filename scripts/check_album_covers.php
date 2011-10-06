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
	
} else {
	$options = array(
		'type' => 'object',
		'subtype' => 'album',
		'limit' => 0,
	);
	
	$albums = elgg_get_entities($options);
	
	foreach($albums as $album) {
		$cover_guid = $album->cover;
		
		if ($album->cover_guid === 0) {
			echo "GUID: {$album->guid} - COVER: {$cover_guid} - NAME: {$album->title}
";
		}
		

	}
}



echo "</pre>";