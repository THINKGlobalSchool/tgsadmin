<?php
/** Extract a user's todo file submissions **/
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

$go = get_input('go', FALSE);

$username = get_input('u', FALSE);

$user = get_user_by_username($username);

global $CONFIG;

$dataroot = $CONFIG->dataroot;

echo "<pre>";

if (elgg_instanceof($user, 'user')) {
	echo "USER: {$user->name}<br /><br />";
	
	$options = array(
		'type' => 'object',
		'subtype' => 'todosubmission',
		'owner_guid' => $user->guid,
		'limit' => 0,
	);
	
	$submissions = new ElggBatch('elgg_get_entities', $options);
	
	echo "SUBMISSION FILES: <br /><br />";
	
	if ($go) {
		$zip = new ZipArchive;

		if ($zip->open($dataroot . $username . '.zip', ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
			die('ERROR CREATING ZIP');
		}	
	}
	
	foreach ($submissions as $submission) {
		
		$todo = get_entity($submission->todo_guid);
		
		if (!$todo) {
			$todo_guid = '(disabled)';
		} else {
			$todo_guid = $todo->guid;
		}
		
		$content = unserialize($submission->content);
		
		if (is_array($content)) {
			foreach ($content as $item) {
				$entity = get_entity($item);
				
				if (elgg_instanceof($entity, 'object', 'file') || elgg_instanceof($entity, 'object', 'todosubmissionfile')) {
					
					echo "TODO GUID: {$todo_guid} - SUBMISSION GUID: {$submission->guid} - FILE GUID: {$entity->guid} - SUBTYPE: {$entity->getSubtype()} - ";
					
					$filename = $entity->getFilenameOnFilestore();
					
					echo $filename . "<br /><br />";
					
					if ($go) {
						if (file_exists($filename)) {
							$zip->addFile($filename, $username . '/' . $entity->getFilename());
							if (!$zip->status == ZIPARCHIVE::ER_OK) {
								echo "ERROR ADDING FILE TO ZIP<br /><br />";
							}
						}
					}
				}
			}
		}
	}
	
	if (!$go) {
		
		$url = elgg_get_site_url() . 'mod/tgsadmin/scripts/extract_todo_file_submissions.php';
		
		echo <<<HTML
			<form method='GET' action='$url'>
				<input type='submit' name='go' value='GO' />
				<input type='hidden' name='u' value='$username' />
			</form>
HTML;
	} else {
		$zip->close();
	}
	
} else {
	echo "INVALID USER";
}

echo "</pre>";
