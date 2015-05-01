<?php
/** 
 * Todo Analytics Exports
 *
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

$delimiter = "Ǿ";

// Custom callback to create CSV rows with required export data
function blog_callback($row) {
		// Get some extra info about the entity (tags, url and comment count)
		$entity = get_entity($row->guid);

		$row->url = $entity->getURL();

		$row->entity_description = str_replace("\r\n","", $row->entity_description);

		$values = array(
			$row->display_name,
			$row->entity_title,
			strip_tags("\"$row->entity_description\""),
			$row->url,
		);
		
		$string = implode('Ǿ', $values);

		return $string;
}

$dbprefix = elgg_get_config('dbprefix');

// Dump entities
if (get_input('blogs')) {
	// No limit
	set_time_limit(0);

	/* Todo's
	Display Name | username | Group | Title | Description | Due Date | Grade required | Submission required | Submission tags | URL
	*/

	$offset = 0;
	$limit = 500;

	$wheres = array(
		"(oe.description REGEXP '[[:<:]]TGS[[:>:]]' || oe.description like '%THINK Global School%')"
	);

	// Entity options
	$options = array(
		'type' => 'object',
		'subtype' => 'blog',
		'created_time_lower' => '1346457600', // Sept 1, 2012 00:00:00
		'callback' => 'blog_callback',
		'selects' => array(
			"ue.name as display_name",
			"oe.title as entity_title",
			"oe.description as entity_description",
		),
		'joins' => array(
			"JOIN {$dbprefix}users_entity ue on e.owner_guid = ue.guid",
			"JOIN {$dbprefix}objects_entity oe on e.guid = oe.guid"
		),
		'wheres' => $wheres,
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
	header("Content-Disposition: attachment; filename=blog_dump.csv");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo "Display Name{$delimiter}Title{$delimiter}Description{$delimiter}URL\r\n";

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
	echo "<h3>Blog Content Export</h3>";
	echo "<a href='blog_export_with_content.php?blogs=1'>Dump Blogs</a><br />";
}