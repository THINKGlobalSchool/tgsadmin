<?php
/** 
 * Blog Text Dump
 *
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

set_time_limit(0);

// Callback for get data
function blog_text_callback($dr) {
	$strippedtags = strip_tags($dr->description);
	$parsed_ecml = ecml_parse_string($strippedtags);
	$parsed_mentions = preg_replace(mentions_get_regex(), '', $parsed_ecml);
	return strip_tags($parsed_mentions);
}

$dbprefix = elgg_get_config('dbprefix');

$subtype_id = get_subtype_id('object', 'blog');

$prv = ACCESS_PRIVATE;

$query = <<<SQL
	SELECT oe.description from {$dbprefix}entities e
	JOIN {$dbprefix}objects_entity oe on e.guid = oe.guid
	WHERE e.type = 'object' 
	AND e.subtype IN ({$subtype_id})
	AND e.access_id NOT IN ({$prv})
SQL;

$data = get_data($query, 'blog_text_callback');

header("Content-type: text/plain");
header("Content-Disposition: inline; filename=text_dump.txt");
header("Pragma: no-cache");
header("Expires: 0");

foreach ($data as $desc) {
	echo $desc . " ";
}

