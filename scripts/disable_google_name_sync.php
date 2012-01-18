<?php
/** Disable Google Name Syncing **/
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

$go = get_input('go', FALSE);

$options = array(
	'type' => 'user',
	'limit' => 0,
);

$users = new ElggBatch('elgg_get_entities', $options);

echo "<pre>";

echo "DISABLE GOOGLE NAME SYNCING <br /><br />";

if (!$go) {
	$url = elgg_get_site_url();
	echo "<form action='{$url}mod/tgsadmin/scripts/disable_google_name_sync.php'>";
	echo "<input type='submit' name='go' value='GO' />";
	echo "</form>";	
}

foreach ($users as $user) {
	if ($go) {
		$sync_settings = unserialize($user->sync_settings);
		$sync_settings['sync_name'] = 0;
		$user->sync_settings = serialize($sync_settings);
		$user->save();
	}

	$sync_settings = unserialize($user->sync_settings);

	if ($user->sync_settings) {
		echo $user->guid . ' - ' . $user->username . ' - '  . $sync_settings['sync_name'] . '<br />';
	}
}

echo "</pre>";
