<?php
/** 
 * Convert webvideo entities to bookmarks entities
 *
 */
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();


// Start the convert!
if (get_input('convert')) {
	set_time_limit(0);

	echo "<pre>";
	$options = array(
		'type' => 'object',
		'subtype' => 'webvideo',
		'limit' => 0
	);

	$webvideos = new ElggBatch('elgg_get_entities', $options);

	// Put video urls through bookmarks save validation
	$php_5_2_13_and_below = version_compare(PHP_VERSION, '5.2.14', '<');
	$php_5_3_0_to_5_3_2 = version_compare(PHP_VERSION, '5.3.0', '>=') &&
			version_compare(PHP_VERSION, '5.3.3', '<');

	$bid = get_subtype_id('object', 'bookmarks');
	if (!$bid) {
		$bid = add_subtype('object', 'bookmarks');
	}
	$wid = get_subtype_id('object', 'webvideo');

	foreach ($webvideos as $video) {
		echo "{$video->guid}";

		$validated = false;
		if ($php_5_2_13_and_below || $php_5_3_0_to_5_3_2) {
			$tmp_address = str_replace("-", "", $video->url);
			$validated = filter_var($tmp_address, FILTER_VALIDATE_URL);
		} else {
			$validated = filter_var($video->url, FILTER_VALIDATE_URL);
		}

		// Validate url
		if ($validated) {
			echo ' - VALIDATED';

			// If we've got both subtype ids, update data
			if ($bid && $wid) {
				$dbprefix = elgg_get_config('dbprefix');
				$e_sql = "UPDATE {$dbprefix}entities SET subtype = {$bid} WHERE guid = {$video->guid}";

				update_data($e_sql);

				echo ' - Entities updated';

				$r1_sql = "UPDATE {$dbprefix}river SET subtype = 'bookmarks' WHERE object_guid = {$video->guid}";

				update_data($r1_sql);

				echo ' - River subtype updated';

				$r2_sql = "UPDATE {$dbprefix}river SET view = 'river/object/bookmarks/create' WHERE object_guid = {$video->guid} AND view ='river/object/webvideo/create'";

				update_data($r2_sql);

				echo ' - River views updated';

				$video->address = $video->url;

				_elgg_invalidate_cache_for_entity($video->guid);

				bookmarks_extender_populate_preview($video);
			}
		} else {
			echo ' - INVALID URL';
		}

		echo "\r\n";
	}

	echo "</pre>";
}  else if (get_input('update')) {
	set_time_limit(0);

	echo "<pre>";
	$options = array(
		'type' => 'object',
		'subtype' => 'bookmarks',
		'limit' => 0
	);

	$bookmarks = new ElggBatch('elgg_get_entities', $options);

	foreach ($bookmarks as $bookmark) {
		echo "UPDATING: {$bookmark->guid}\r\n";
		$bookmark->preview_image = null;
		_elgg_invalidate_cache_for_entity($bookmark->guid);
		bookmarks_extender_populate_preview($bookmark);
	}

	echo "</pre>";
} else {
	// Destiny Links
	echo "<h3>webvideos => bookmarks</h3>";
	echo "<a href='webvideos_to_bookmarks.php?convert=1'>Start conversion</a><br />";
	echo "<a href='webvideos_to_bookmarks.php?update=1'>Update bookmark previews</a><br />";
}