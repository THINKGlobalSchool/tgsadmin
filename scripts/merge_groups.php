<?php
/** Merge content of one group to another **/
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

$group_1 = get_entity(get_input('g1'));
$group_2 = get_entity(get_input('g2'));
$go = get_input('go', FALSE);

echo "<pre>";

if (elgg_instanceof($group_1, 'group') && elgg_instanceof($group_2, 'group')) {
	echo "Destination Group: {$group_1->name}<br />";
	echo "Source Group: {$group_2->name}<br />";
	
	$options = array(
		'type' => 'object',
		'container_guid' => $group_2->guid,
		'limit' => 0,
		'count' => TRUE,
 	);

	$count = elgg_get_entities($options);
	
	echo "<br />Entities in source group: {$count}<br /><br />";
			
	unset($options['count']);
	
	$entities = elgg_get_entities($options);
	
	foreach($entities as $entity) {
		if ($go) {
			$entity->container_guid = $group_1->guid;
			if ($entity->save()) {
				$status = "MOVED TO: {$group_1->guid} - ";
			}
		}
		echo "$status GUID: {$entity->guid} - Subtype: {$entity->getSubtype()}<br />";
		
	}

	if (!$go) {
		$url = elgg_get_site_url();
		echo "<br /><form action='{$url}mod/tgsadmin/scripts/merge_groups.php'>";
		echo "<input type='submit' name='go' value='GO' />";
		echo "<input type='hidden' name='g1' value='{$group_1->guid}' />";
		echo "<input type='hidden' name='g2' value='{$group_2->guid}' />";
		echo "</form>";
	}

} else {
	echo 'Invalid Group(s)';
}

echo "</pre>";