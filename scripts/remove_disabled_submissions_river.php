<?php
/** Remove disabled todo submission river entries **/
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
global $CONFIG;
admin_gatekeeper();

echo "<h1>REMOVE DISABLED TODO SUBMISSION RIVER ENTRIES</h1><pre>";

$options = array(
	'type' => 'object',
	'subtype' => 'todo',
	'limit' => 0,
);
access_show_hidden_entities(TRUE);
$todos = new ElggBatch('elgg_get_entities', $options);

foreach ($todos as $todo) {
	if ($todo->enabled == 'no') {
		echo "TODO: {$todo->guid} - Enabled: {$todo->enabled}\r\n";
		
		// Get submissions
		$submissions = get_todo_submissions_batch($todo->guid, 0);
		
		foreach ($submissions as $submission) {
			echo "	Submission: {$submission->guid} \r\n";
			if (get_input('go')) {
				// Remove from river
				elgg_delete_river(array(
					'object_guid' => $submission->guid,
					'action_type' => 'create',
				));
				echo "\r\n	Deleted! \r\n \r\n";
			}
		}
	}
}

access_show_hidden_entities(FALSE);
echo "</pre>";
