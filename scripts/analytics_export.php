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
			$row->subtype_name,
			$row->group_name,
			$row->entity_title,
			$row->access_id_name,
			$row->tags,
			$row->comment_count,
			$row->url,
		);
		
		$string = implode(',', $values);

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
			"LEFT JOIn {$dbprefix}access_collections ac on e.access_id = ac.id",
		),
		'limit' => 0, 
	);

	$result = elgg_get_entities_from_metadata($options);

	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=entities_dump.csv");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo "Display Name,Username,Date/Time Created,Content Type,Group,Title,Access Level,Tags,Comments,URL\r\n";

	foreach ($result as $row) {
		echo $row . "\r\n";
	}
} else if (get_input('comments')) { // Dump comments
	/* Generic Comments
	Display Name | Username | Date/Time Created | Content Type | Commented On | URL
	*/

	$options = array(
		'annotation_name' => 'generic_comment',
		'annotation_created_time_lower' => '1346457600', // Sept 1, 2012 00:00:00
		'callback' => 'annotations_analytics_callback',
		'selects' => array(
			"ue.username as username",
			"ue.name as display_name",
			"e_sub.subtype as subtype_name",
		),
		'joins' => array(
			"JOIN {$dbprefix}users_entity ue on n_table.owner_guid = ue.guid",
			"JOIN {$dbprefix}entities entities on n_table.entity_guid = entities.guid",
			"JOIN {$dbprefix}entity_subtypes e_sub on entities.subtype = e_sub.id",
		),
		'limit' => 0,
	);

	$result = elgg_get_annotations($options);

	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=comments_dump.csv");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo "Display Name,Username,Date/Time Created,Content Type Commented On,URL\r\n";

	foreach ($result as $row) {
		echo $row . "\r\n";
	}


} else {
	// Destiny Links
	echo "<h3>Analytics Export - Choose your destiny</h3>";
	echo "<a href='analytics_export.php?entities=1'>Dump entities</a><br />";
	echo "<a href='analytics_export.php?comments=1'>Dump comments</a>";
}