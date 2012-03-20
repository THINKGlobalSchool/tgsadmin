<?php
/** Remove duplicate todo submissions **/
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
global $CONFIG;
admin_gatekeeper();

echo "<h1>REMOVE DUPLICATE TODO SUBMISSIONS</h1><pre>";

$db_prefix = elgg_get_config('dbprefix');

$options = array(
	'type' => 'object',
	'subtype' => 'todo',
	'limit' => 0,
);

$todos = new ElggBatch('elgg_get_entities', $options);
$dupe_count = 0;

foreach ($todos as $todo) {		
	$s_options = array(
		'relationship' => 'submittedto',
		'relationship_guid' => $todo->guid,
		'type' => 'object',
		'inverse_relationship' => TRUE,
		'subtype' => 'todosubmission',
		'limit' => 0,
	);
	
	$submissions = elgg_get_entities_from_relationship($s_options);	

	$user_submissions = array();
	
	foreach ($submissions as $submission) {
		$user_submissions[$submission->owner_guid]['count'] += 1; 
		$user_submissions[$submission->owner_guid]['submissions'][] = $submission->guid;
	}

	foreach($user_submissions as $idx => $us) {
		if ($user_submissions[$idx]['count'] > 1) {
			$max = max($user_submissions[$idx]['submissions']);
			
			echo "DUPLICATES FOR USER: {$idx} TODO: {$todo->guid} RETURN REQUIRED: {$todo->return_required}\r\n";
			foreach ($user_submissions[$idx]['submissions'] as $s) {
				$dupe_count++;

				if ($s == $max) {
					$m = '<-- KEEP!';
				} else {
					$m = '<-- del';
					if (get_input('go')) {
						$submission = get_entity($s);
						$submission->delete(); // Do it..
						$m = '<------- DELETED!!!';
					}
				}
				
				echo "	$s $m\r\n";
			}
		}
	}
}

echo "\r\nDUPE COUNT: $dupe_count\r\n";

echo "</pre>";