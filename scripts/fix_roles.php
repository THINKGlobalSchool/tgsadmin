<?php
/** Fix unhidden roles **/
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
global $CONFIG;
admin_gatekeeper();

echo "<h1>FIX UNHIDDEN ROLES</h1><pre>";

$options = array(
	'type' => 'object',
	'subtype' => 'role',
	'limit' => 0,
);

$roles = new ElggBatch('elgg_get_entities', $options);

foreach ($roles as $role) {
	echo "Role: {$role->guid} - Hidden: {$role->hidden}\r\n";
	if (get_input('go')) {
		if (!$role->hidden) {
			$role->hidden = 0;
			echo "\r\n	Set! \r\n \r\n";
		}
	}	
}

echo "</pre>";
