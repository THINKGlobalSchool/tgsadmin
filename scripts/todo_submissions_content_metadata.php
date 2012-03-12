<?php
/** Update submission content metadata **/
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
global $CONFIG;
admin_gatekeeper();

echo "<h1>Update todo submission metadata</h1><pre>";

$options = array(
	'type' => 'object',
	'subtype' => 'todosubmission',
	'limit' => 0,
);


$submissions = new ElggBatch('elgg_get_entities', $options);

foreach ($submissions as $submission) {
	if (!$submission->content || $submission->content == 'N;' || $submission->content == 's:0:"";') {
		echo "{$submission->guid} \r\n- {$submission->content}\r\n\r\n";
		if (get_input('go')) {
			$submission->content = 0;
		}
	}
}

echo "</pre>";