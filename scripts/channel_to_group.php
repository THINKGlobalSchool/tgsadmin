<?php
/** Move a channel's content to a group ACL **/
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

$channel_guid = get_input('c', FALSE);
$group_guid = get_input('g', FALSE);

$channels = elgg_get_entities(array(
	'type' => 'object',
	'subtype' => 'shared_access',
	'limit' => 0,
));

$groups = elgg_get_entities(array(
	'type' => 'group',
	'limit' => 0,
));

echo "<pre>";
// Check for channel/group guids
if ($channel_guid && $group_guid) {
	$channel = get_entity($channel_guid);
	$group = get_entity($group_guid);

	if (elgg_instanceof($channel, 'object', 'shared_access') && elgg_instanceof($group, 'group')) {
		
		$options = array(
			'limit' => 0,
			'access_id' => $channel->acl_id,
		);
	
		$entities = elgg_get_entities_from_access_id($options);
		
		echo $channel->title . "
GUID: " . $channel->guid . " ACL: " . $channel->acl_id ." 

";
		echo $group->name . "
GUID: " . $group->guid . " ACL: " . $group->group_acl." 

";
		$updated = 0;

		// Set entities container_guid and ACL
		foreach($entities as $entity) {
			if ($entity->getSubtype != "tidypics_batch" && $entity->getSubtype != "image") {
				$entity->container_guid = $group->guid;
				$entity->access_id = $group->group_acl;
				if ($entity->save()) {
					$updated++;
				}
			}
		}
		
		echo "Updated: " . $updated;
		
		
	} else {
		echo "Invalid group/channel";
	}
} else { // Show stats
	echo "Existing Channels
-----------------
";

	foreach ($channels as $channel) {
	
		$options = array(
			'limit' => 0,
			'count' => TRUE,
			'access_id' => $channel->acl_id,
		);
	
		$count = elgg_get_entities_from_access_id($options);
		
		$options['count'] = FALSE;
		
		$entities = elgg_get_entities_from_access_id($options);
	
	
		echo "
" . $channel->title . "
GUID: " . $channel->guid . " ACL: " . $channel->acl_id ." ENTITIES: " . $count . " 

";

		foreach($entities as $entity) {
			echo $entity->getSubtype() . "
";
		}
	}

	echo "
Existing Groups
---------------
";

	foreach ($groups as $group) {
		echo $group->name . "
GUID: " . $group->guid . " ACL: " . $group->group_acl." 

";
	}
}
echo "</pre>";