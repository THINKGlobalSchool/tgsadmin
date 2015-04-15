<?php
/** 
 * Migrate Google Apps Connect Metadata
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

$db_prefix = elgg_get_config('dbprefix');

echo "<pre>";
echo "====== GOOGLE METADATA MIGRATION ======\n\n";

$users = elgg_get_entities(array(
	'type' => 'user',
	'joins' => "JOIN {$db_prefix}users_entity ue on e.guid = ue.guid",
	'wheres' => array(
		"ue.email like '%thinkglobalschool.com%'"
	),
	'limit' => 0
));

echo "FOUND TGS USERS:\n----------------\n\n";

foreach ($users as $user) {
	echo str_pad($user->username, 20, " ", STR_PAD_RIGHT) . "|      "  . $user->email . "\n";
}

echo "\nUPDATING METADATA:\n------------------\n\n";

$meta_options = array(
	'limit' => 0,
	'metadata_names' => array(
		'sync', 'connect', 'googleapps_controlled_profile', 'google', 'google_access_token', 
		'google_refresh_token', 'access_token', 'access_token', 'google_connected'
	),
);

foreach ($users as $user) {
	$meta_options['guid'] = $user->guid;
	if (elgg_delete_metadata($meta_options)) {
		echo str_pad($user->username , 20, " ", STR_PAD_RIGHT) . " ----> REMOVED: [google, sync, googleapps_controlled_profile, connect]\n";
	} else {
		echo str_pad($user->username , 20, " ", STR_PAD_RIGHT) . " ----> ERROR REMOVING METADATA!!\n";
	}
	

	$user->google_connected = 1;
	$user->save();	
	echo str_pad("", 20, " ", STR_PAD_RIGHT) . " ----> ADDED: google_connected = 1 \n\n";
}