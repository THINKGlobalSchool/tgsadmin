<?php
/** 
 * Todo Analytics Exports
 *
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

// Custom callback to create CSV rows with required export data
function todo_analytics_callback($row) {
		// Get some extra info about the entity (tags, url and comment count)
		$entity = get_entity($row->guid);
		
		$tags = $entity->suggested_tags;

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

		$row->submission_tags = $tag_string;

		$row->due_date = $entity->due_date;
		$row->grade_required = $entity->grade_required;
		$row->return_required = $entity->return_required;

		$row->url = $entity->getURL();

		$row->entity_description = str_replace("\r\n","", $row->entity_description);

		$values = array(
			$row->display_name,
			$row->username,
			$row->group_name,
			$row->entity_title,
			"\"$row->entity_description\"",
			$row->due_date,
			$row->grade_required,
			$row->return_required,
			$row->submission_tags,
			$row->url,
		);
		
		$string = implode("\t", $values);

		return $string;
}

$dbprefix = elgg_get_config('dbprefix');

// Dump entities
if (get_input('todos')) {
	// No limit
	set_time_limit(0);

	/* Todo's
	Display Name | username | Group | Title | Description | Due Date | Grade required | Submission required | Submission tags | URL
	*/

	$offset = 0;
	$limit = 500;

	// Entity options
	$options = array(
		'type' => 'object',
		'subtype' => 'todo',
		'created_time_lower' => '1346457600', // Sept 1, 2012 00:00:00
		'callback' => 'todo_analytics_callback',
		'selects' => array(
			"ue.username as username",
			"ue.name as display_name",
			"ge.guid as group_guid",
			"ge.name as group_name",
			"oe.title as entity_title",
			"oe.description as entity_description",
		),
		'joins' => array(
			"JOIN {$dbprefix}users_entity ue on e.owner_guid = ue.guid",
			"JOIN {$dbprefix}objects_entity oe on e.guid = oe.guid",
			"LEFT JOIN {$dbprefix}groups_entity ge on e.container_guid = ge.guid", // Left join includes null if no group
		),
		'limit' => 0,
		'offset' => $offset,
		'count' => true, 
	);

	// Get a count
	$count = elgg_get_entities($options);

	unset($options['count']);

	$options['limit'] = $limit;

	$chunks = ceil($count / $limit);

	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=todo_dump.csv");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo "Display Name\tUsername\tGroup\tTitle\tDescription\tDue Date\tGrade required\tSubmission required\tSubmission tags\t URL\r\n";

	// Chunk the output
	for ($i = 0; $i < $chunks; $i++) {
		$options['offset'] = $offset;
		$result = new ElggBatch('elgg_get_entities', $options);
		foreach ($result as $row) {
			echo $row . "\r\n";
		}
		$offset += $limit;
	}

} else {
	// Destiny Links
	echo "<h3>Todo Analytics Export</h3>";
	echo "<a href='todo_analytics_export.php?todos=1'>Dump Todos</a><br />";
}