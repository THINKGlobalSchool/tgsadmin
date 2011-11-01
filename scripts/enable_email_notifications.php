<?php
/** Enable email notifications for all site users **/
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

$go = get_input('go', FALSE);

$options = array(
	'type' => 'user',
	'limit' => 0,
);

$users = new ElggBatch('elgg_get_entities', $options);

echo "<pre>";

echo "ENABLE USER EMAIL NOTIFICATIONS <br /><br />";

if (!$go) {
	$url = elgg_get_site_url();
	echo "<form action='{$url}mod/tgsadmin/scripts/enable_email_notifications.php'>";
	echo "<input type='submit' name='go' value='GO' />";
	echo "</form>";	
}

foreach ($users as $user) {
	if ($go) {
		$email_set .= " -> YES";
		set_user_notification_setting($user->guid, 'email', TRUE);
		echo "$user->username - DONE!<br />";
	}
}
echo "</pre>";