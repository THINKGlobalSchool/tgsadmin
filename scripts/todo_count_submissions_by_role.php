<?php
/** 
 * Todo Submission Account By Role
 *
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

$role_id = get_input('role_id', false);
$role = get_entity($role_id);

echo "<h3>Todo Submission Count By Role</h3>";
echo "<pre>";

if (!elgg_instanceof($role, 'object', 'role')) {
	echo "Invalid Role";
	exit;
} 

$role_members = $role->getMembers(0);

$submission_count = 0;

foreach ($role_members as $member) {
	$submissions = elgg_get_entities(array(
		'owner_guid' => $member->guid,
		'type' => 'object',
		'subtype' => 'todosubmission',
		'count' => true
	));

	echo "{$member->username} - {$submissions}\r\n";

	$submission_count += $submissions;
}

echo "\r\nTOTAL: $submission_count";


echo "</pre>";