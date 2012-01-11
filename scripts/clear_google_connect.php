<?php
	/** Script to remove a users google apps connection **/
	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	global $CONFIG;
	admin_gatekeeper();
	
	$url = $CONFIG->url . "mod/tgstweaks/clear_google_connect.php";

	$user_input = elgg_view('input/text', array('name' => 'user'));
	
	if (get_input('submit') && $user = get_user(get_input('user'))) {
		echo <<<EOT
		<h3>Before: </h3</br>
		Sync: {$user->sync}<br />
		Subtype: {$user->subtype}<br />
		Connect: {$user->connect}<br />
		Controlled Profile: {$user->googleapps_controlled_profile}<br />
		Access Token: {$user->access_token}<br />
		Secret Token: {$user->token_secret}<br />
		Google: {$user->google}<br />
EOT;
		$user->sync = '0';
		$user->subtype = '';
		$user->connect = 0;
		$user->googleapps_controlled_profile = 'no';
		$user->google = 0;
		$user->access_token = '';
		$user->token_secret = '';
		$user->email = '';
		$user->save();
		
		echo <<<EOT
		<br /><br /><h3>After: </h3</br>
		Sync: {$user->sync}<br />
		Subtype: {$user->subtype}<br />
		Connect: {$user->connect}<br />
		Controlled Profile: {$user->googleapps_controlled_profile}<br />
		Access Token: {$user->access_token}<br />
		Secret Token: {$user->token_secret}<br />
		Google: {$user->google}<br />
EOT;
		
	} else {
		echo <<<EOT
		<h2>Clear Google Connect</h2>
		<form action='$url' method='POST'>
			<label>User GUID: </label>
			$user_input<br />
			<input type='submit' name='submit' value='Submit' />
		</form>
EOT;
	}

?>

