<?php
/** 
 * Set announcement expiry
 *
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

$go = get_input('go', FALSE);

echo "<pre>SET DEFAULT ANNOUNCEMENT EXPIRY<br /><br />";

$options = array(
	'type' => 'object',
	'subtype' => 'announcement',
	'limit' => 0,
);

$announcements = new ElggBatch('elgg_get_entities', $options);

echo "ANNOUNCEMENTS:<br/>--------------<br />";

// Check each announcement
foreach ($announcements as $announcement) {
	if (empty($announcement->expiry_date) || $announcement->expiry_date == NULL) {
		// Set existing group entities to never expire
		if (elgg_instanceof($announcement->getContainerEntity(), 'group')) {
			$expiry_date = 2145938400;
		} else {
			$expiry_date = strtotime(date('d-m-Y', strtotime("+1 week", $announcement->time_created)));
		}
		
		if ($go) {
			// Set expiry to +1 week
			$announcement->expiry_date = $expiry_date;
			$set = "SET! {$announcement->expiry_date}";
		}
		echo "<br />{$announcement->guid} - {$announcement->time_created} -> {$expiry_date} {$set}<br /><br/>";
	}	
}

echo "</pre>";