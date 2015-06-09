<?php
/** 
 * Analytics Exports
 *
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

// Custom callback to create CSV rows with required export data
function entity_analytics_callback($row) {
		// Get some extra info about the entity (tags, url and comment count)
		$entity = get_entity($row->guid);
		
		$tags = $entity->tags;

		if (is_array($tags)) {
			foreach ($tags as $tag) {
				$tag_string .= "[$tag]";
			}
		} else {
			if (!empty($tags)) {
				$tag_string = "[$tags]";
			} else {
				$tag_string = NULL;
			}
		}

		if (!$row->access_id_name) {
			$row->access_id_name = get_readable_access_level($row->access_id);
		}

		$row->tags = $tag_string;

		$row->url = $entity->getURL();
		$row->comment_count = $entity->countComments();

		$values = array(
			$row->display_name,
			$row->username,
			$row->time_created,
			$row->entity_title,
			$row->url,
			$row->downloadUrl
		);
		
		$string = implode("\t", $values);

		return $string;
}

// Custom callback for elgg_get_annotations
function annotations_analytics_callback($row) {
	$entity = get_entity($row->entity_guid);
	$row->url = $entity->getURL();

	$values = array(
		$row->display_name,
		$row->username,
		$row->time_created,
		$row->subtype_name,
		$row->url,
	);

	$string = implode(',', $values);

	return $string;
}

$dbprefix = elgg_get_config('dbprefix');

// Dump entities
if (get_input('entities')) {
	// No limit
	set_time_limit(0);

	/* Entities
	Display Name | Username | Date/Time Created | Content Type | Group | Title | Access Level | Tags | Comments | URL
	*/

	$offset = 0;
	$limit = 500;

	// Entity options
	$options = array(
		'type' => 'object',
		'created_time_lower' => '1346457600', // Sept 1, 2012 00:00:00
		'callback' => 'entity_analytics_callback',
		'selects' => array(
			"e_sub.subtype as subtype_name",
			"ue.username as username",
			"ue.name as display_name",
			"ge.guid as group_guid",
			"ge.name as group_name",
			"oe.title as entity_title",
			"ac.name as access_id_name",
		),
		'joins' => array(
			"JOIN {$dbprefix}entity_subtypes e_sub on e.subtype = e_sub.id",
			"JOIN {$dbprefix}users_entity ue on e.owner_guid = ue.guid",
			"JOIN {$dbprefix}objects_entity oe on e.guid = oe.guid",
			"LEFT JOIN {$dbprefix}groups_entity ge on e.container_guid = ge.guid", // Left join includes null if no group
			"LEFT JOIN {$dbprefix}access_collections ac on e.access_id = ac.id",
		),
		'limit' => 0,
		'offset' => $offset,
		'count' => true, 
	);

	
	$options['subtype'] = "simplekaltura_video";


	// Get a count
	$count = elgg_get_entities_from_metadata($options);

	unset($options['count']);

	$options['limit'] = $limit;

	$chunks = ceil($count / $limit);

	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=video_dump.csv");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo "Display Name\tUsername\tDate/Time Created\tTitle\tURL\tDownload\r\n";

	// Chunk the output
	for ($i = 0; $i < $chunks; $i++) {
		$options['offset'] = $offset;
		$result = new ElggBatch('elgg_get_entities_from_metadata', $options);
		foreach ($result as $row) {
			echo $row . "\r\n";
		}
		$offset += $limit;
	}

} else {
	// Destiny Links
	echo "<h3>Video Export - Choose your destiny</h3>";
	echo "<a href='video_export.php?entities=1'>Dump Videos</a><br />";
}