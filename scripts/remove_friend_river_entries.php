<?php

/** Remove friend river entries **/
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
global $CONFIG;
admin_gatekeeper();

$go = get_input('go', FALSE);

echo "DELETE FRIEND RIVER ENTRIES<br />";

if ($go) {
	elgg_delete_river(array(
		'type' => 'user',
		'view' => 'river/relationship/friend/create',
	));
	echo "DELETED!!";
}