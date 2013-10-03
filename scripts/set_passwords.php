<?php
/** 
 * Set all user passwords - for testing features on STAGING ONLY
 */
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

echo "<h2>Set all user passwords</h2>";

$password = get_input('password', false);

if (!$password) {
	echo "<form method='GET' action=''>";
	echo "Password: <input type='text' name='password' />";
	echo "<input type='submit' name='submit_pass' value='Submit' /><br />";
	echo "</form>";

} else {
	echo "<pre>";

	$users = elgg_get_entities(array(
		'type' => 'user',
		'limit' => 0
	));

	foreach ($users as $user) {
		if (!$user->isAdmin()) {
			$user->salt = generate_random_cleartext_password();
			$user->password = generate_user_password($user, $password);
			$user->save();
			echo "Setting: {$user->username}<br />";
		}
	}

	echo "</pre>";

}




