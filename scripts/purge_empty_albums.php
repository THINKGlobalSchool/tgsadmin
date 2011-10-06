<?php
// Purge tidypics albums older than given date
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
global $CONFIG;
admin_gatekeeper();
elgg_set_ignore_access(TRUE);

$upper_date = get_input('upper', time());

$go = get_input('go', FALSE);



$options = array(
	'type' => 'object',
	'subtype' => 'album',
	'limit' => 0,
	'created_time_upper' => $upper_date,
);

$albums = elgg_get_entities($options);

$text_date = date("F j, Y, g:i a", $upper_date);

echo "<pre>";
echo "EMPTY ALBUMS CREATED BEFORE: {$text_date}

";

foreach($albums as $album) {
	
	$options = array(
		'type' => 'object',
		'subtype' => 'image',
		'limit' => 0,
		'container_guid' => $album->guid,
		'count' => TRUE,
	);
	
	$photo_count = elgg_get_entities($options);
	
	if (!$photo_count) {
		
		if ($go) {
			$confirm = '[DELETED!]';
		}
		
		echo "GUID: {$album->guid} - NAME: {$album->title} - PHOTOS: {$photo_count} {$confirm}
";
		if ($go) {
			$album->delete();
		}
			
	}
	

}

echo "</pre>";

if (!$go) {
	echo <<<HTML
		<form action='purge_empty_albums.php' method='GET'>
			<input type='submit' value='Delete!' name='go' />
			<input type='hidden' value='$upper_date' name='upper' />
		</form>
HTML;
}

elgg_set_ignore_access(FALSE);