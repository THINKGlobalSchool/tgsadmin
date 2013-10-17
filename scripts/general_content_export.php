<?php
/** 
 * General content export
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

// Custom callback to create CSV rows with required export data
function general_export_callback($row) {
		// Get some extra info about the entity (tags, url and comment count)
		$entity = get_entity($row->guid);
		
		$tags = $entity->suggested_tags;

		$row->url = $entity->getURL();

		$values = array(
			$row->display_name,
			date("F j, Y", $row->time_created),
			$row->url,
		);
		
		$string = implode(",", $values);

		return $string;
}

$dbprefix = elgg_get_config('dbprefix');

$subtype = get_input('subtype');
$created_time_lower = get_input('created_time_lower');

// Dump entities
if ($subtype && $created_time_lower) {
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
		'subtype' => $subtype,
		'created_time_lower' => $created_time_lower,
		'callback' => 'general_export_callback',
		'selects' => array(
			"ue.name as display_name",
			"ge.guid as group_guid",
			"ge.name as group_name"
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
	header("Content-Disposition: attachment; filename={$subtype}_dump.csv");
	header("Pragma: no-cache");
	header("Expires: 0");

	// header("Content-type: text/html");
	// header("Content-Disposition: inline;");
	// header("Pragma: no-cache");
	// header("Expires: 0");

	echo "User,Created,URL\r\n";

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
	echo "<h3>General Content Export</h3>";
	echo "<p>You need to hit this url with a query like so: ?subtype=blog&created_time_lower=1346457600</p>";
}