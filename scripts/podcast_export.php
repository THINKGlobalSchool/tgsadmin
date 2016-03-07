<?php
/** 
 * Export User Podcasts
 *
 */
ini_set('max_execution_time', 300);
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

$username = get_input('username', false);
$user = get_user_by_username($username);

echo "<h3>Export Podcasts</h3>";
echo "<pre>";

if (!elgg_instanceof($user, 'user')) {
	echo "Invalid User";
	exit;
} 

$podcasts = elgg_get_entities(array(
	'type' => 'object',
	'subtype' => 'podcast',
	'limit' => 0,
	'owner_guid' => $user->guid
));

if (count($podcasts)) {

	$files = array();

	foreach ($podcasts as $podcast) {
		$files[$podcast->guid]['filename'] = $podcast->getFilenameOnFilestore();
		$files[$podcast->guid]['title'] = $podcast->title;
		echo $podcast->guid . " - "  . $podcast->getFilenameOnFilestore() . "\r\n";
	}

	$dataroot = elgg_get_config('dataroot');

	// Try to create todo export directory
	$podcast_export_dir = "{$dataroot}podcast_export";
	if (!file_exists($podcast_export_dir)) {
		mkdir($podcast_export_dir);
	}

	$uid = uniqid();

	$filename = "{$user->username}-{$uid}-podcast-export.zip";

	$filelocation = "{$podcast_export_dir}/{$filename}";

	// Create a new zip
	$zip = new ZipArchive;

	// Try opening
	if ($zip->open($filelocation, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
		echo "error opening a new zip file";
		exit;
	}

	// Add files to zip
	foreach ($files as $guid => $file) {
		// Double-check that file exists
		if (file_exists($file['filename'])) {
			// Get file info
			$file_info = pathinfo($file['filename']);
			$file_extension = $file_info['extension'];

			$title = elgg_get_friendly_title($file['title']);

			// Set a friendlier file output name
			$file_out = "{$user->username}_{$guid}_{$title}.{$file_extension}";

			// Add to zip
			$zip->addFile($file['filename'], $file_out);

			// Check for errors
			if (!$zip->status == ZIPARCHIVE::ER_OK) {
				echo elgg_echo('todo:error:zipfileerror', array($file['filename']));
				exit;
			}
		}
	}

	// Close zip
	$zip->close();

	$zip_base = basename($filelocation);

	// disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Pragma: public");
	header("Expires: 0");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate, post-check=0, pre-check=0");
    header("Last-Modified: {$now} GMT");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$zip_base}");
    header("Content-Transfer-Encoding: binary");

    ob_clean();
	flush();
	readfile($filelocation);
	exit;

} else {
	echo "User has no podcasts!";
}


echo "</pre>";