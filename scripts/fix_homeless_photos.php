<?php
/** Fix/relocate photos without albums **/
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
global $CONFIG;
admin_gatekeeper();

$go = get_input('go', false);

echo "<h2>FIX HOMELESS PHOTOS</h2><pre>";

$photos = elgg_get_entities(array(
	'type' => 'object',
	'subtype' => 'image',
	'limit' => 0, 
	'joins' => array(
		'JOIN elgg_users_entity ue on e.container_guid = ue.guid'
	)
));

echo "<pre>";

echo "Found: " . count($photos) . "\n";

$owner_guids = array();

if ($photos) {
	echo "Processing unique users..\n\n";
	foreach ($photos as $photo) {
		$owner_guids[] = $photo->owner_guid;
	}

	$owner_guids = array_unique($owner_guids);

	$owner_album_guids = array();

	foreach ($owner_guids as $guid) {
		echo "User: - {$guid}\n";
		$user_albums = elgg_get_entities(array(
			'type' => 'object',
			'subtype' => 'album',
			'limit' => 0,
			'container_guid' => $guid,
		));

		$lostalbum = false;

		foreach ($user_albums as $album) {
			if (strpos($album->title, "lostandfound") !== false) {
				echo "\n 	Lost and found album exists - " . $album->guid ."\n";
				$lostalbum = $album->guid;
				break;
			}
		}

		if (!$lostalbum) {
			echo "\n 	Lost and found doesn't exist..";

			if ($go) {
				$lostalbum = 1234;

				$a = new TidypicsAlbum();
				$a->container_guid = $guid;
				$a->owner_guid = $guid;
				$a->access_id = ACCESS_PRIVATE;
				$a->title = 'lostandfound';

				if ($a->save()) {
					$lostalbum = $a->guid;
					echo "\n 	-> created album: $lostalbum \n\n";
				} else {
					echo "\n 	-> Error creating album!";
				}
				
			} else {
				echo "\n";
			}
		}

		$owner_album_guids[$guid] = $lostalbum;
	}
}

foreach ($photos as $photo) {
	$owner = $photo->getOwnerEntity();

	echo "\nProcessing homeless photos\n\n";

	echo $photo->guid . " - " . $owner->name . "\n";

	$new_album = get_entity($owner_album_guids[$owner->guid]);

	if (elgg_instanceof($new_album, 'object', 'album') && $new_album->title == 'lostandfound') {
		echo "\n Got lostandfound album ({$album->guid})\n";

		if ($go) {
			$photo->container_guid = $new_album->guid;
			$photo->save();
			$new_album->prependImageList(array($photo->guid));
			echo "\n 	-> Photo moved!";
		}

	} else {
		echo "\n No lostandfound album, skipping\n";
	}
}

echo "</pre>";