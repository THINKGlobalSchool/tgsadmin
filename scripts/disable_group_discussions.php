<?php
/** 
 * Migrate Group Discussions to Group Forum 
 *
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

$go = get_input('go', FALSE);
$guid = get_input('guid', NULL);

echo "<pre>DISABLE GROUP DISCUSSIONS<br /><br />";

$options = array(
	'type' => 'group',
	'limit' => 0,
	'guid' => $guid,
);

$groups = new ElggBatch('elgg_get_entities', $options);

echo "GROUPS:<br/>-------<br />";

// Check each group for disussions and migrate when required
foreach ($groups as $group) {
	if ($group->forum_enable != 'no') {
		$forum_enabled = 'ENABLED';

		if ($go) {
			$disabled = "-----> DISABLED";
			$group->forum_enable = 'no';
		}

		echo "<br />{$group->guid} - {$group->name} - {$forum_enabled} {$disabled}<br /><br/>";
	}	
}



echo "</pre>";