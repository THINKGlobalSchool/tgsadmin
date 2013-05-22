<?php
	/** Script to change group wire posts access id **/
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
	
	global $CONFIG;
	
	admin_gatekeeper();

	echo elgg_view_title('Change group wire access ids');

	if (get_input('submit_group_wire') && get_input('group_guid') && get_input('access_id')) {
		$wire_posts = elgg_get_entities(array(
			'type' => 'object',
			'subtype' => 'thewire',
			'container_guid' => get_input('group_guid'),
			'limit' => 0,
		));

		$access_id = get_input('access_id');
 
 		$count = 0;
		foreach ($wire_posts as $post)  {
			if ($post->access_id != $access_id) {
				echo $post->guid . " - old: {$post->access_id} -> new: {$access_id}<br />";
				$post->access_id = $access_id;
				$post->save();
				$count++;
			}
		}

		if ($count == 0) {
			echo "No results.";
		}

	} else {
		$groups = elgg_get_entities(array(
			'type' => 'group', 
			'limit' => 0,
		));

		$group_access_array = array();

		foreach ($groups as $group) {
			$group_access_array[$group->guid] = $group->name;
		}

		global $CONFIG;
		$url = $CONFIG->url . "mod/tgsadmin/scripts/change_group_wire_access.php";

		echo "<form method='POST' action='$url'>";
		echo "<h4>Select Group</h4>";

		$group_input = elgg_view('input/dropdown', array(
			'name' => 'group_guid',
			'options_values' => $group_access_array,
		));

		echo $group_input;
		
		echo "<h4>Select Access ID</h4>";

		echo elgg_view('input/access', array(
			'name' => 'access_id',
		));

		echo "<br /><br /><input type='submit' value='Submit' name='submit_group_wire' /></form>";
	}