<?php
	/** Script to move wire posts/site activity to/from an access_id **/
	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	
	global $CONFIG;
	
	admin_gatekeeper();

	$access_from_input = elgg_view('input/access', array('internalname' => 'access_from')); 
	
	$access_to_input = elgg_view('input/access', array('internalname' => 'access_to')); 
	
	$url = $CONFIG->url . "mod/tgstweaks/move_wire_posts.php";

	if (get_input('submit_wire') && get_input('access_from') && get_input('access_to')) {
		$wire_posts = elgg_get_entities_from_access_id(array('type'=>'object', 'subtype'=>'thewire', 'access_id' => get_input('access_from'), 'limit'=>9999));	
		$count = 0;
		foreach ($wire_posts as $post) {
			$post->access_id = get_input('access_to');
			$post->save();
			$count++;
		}
		echo "<h2>Wire Post Access</h2><br />Updated " . $count . " post(s)";
	} else if (get_input('submit_activity') && get_input('access_from') && get_input('access_to')) {
		$site_activity = elgg_get_entities_from_access_id(array('type'=>'object', 'subtype'=>'site_activity', 'access_id' => get_input('access_from'), 'limit'=>9999));	
		$count = 0;
		foreach ($site_activity as $activity) {
			$activity->access_id = get_input('access_to');
			$activity->save();
			$count++;
		}
		echo "<h2>Site Activity Access</h2><br />Updated " . $count . " item(s)";
	} else {
		echo "
			<form method='POST' action='$url'>
				<h1>Set Wire Post Accesss</h1>
				<label>Select access from: </label><br />
				$access_from_input<br /><br />
				<label>Select access to: </label><br />
				$access_to_input<br /><br />
				<input type='submit' name='submit_wire' />
				</form>
			";
			
		echo "
			<form method='POST' action='$url'>
				<h1>Set Site Activity Accesss</h1>
				<label>Select access from: </label><br />
				$access_from_input<br /><br />
				<label>Select access to: </label><br />
				$access_to_input<br /><br />
				<input type='submit' name='submit_activity' />
				</form>
			";
	}
?>

