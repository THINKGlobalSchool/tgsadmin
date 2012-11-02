<?php
/**
 * Elgg TGSAdmin move entity action
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2012
 * @link http://www.thinkglobalschool.com
 *
 */

$entity_guid = get_input('entity_guid');
$group_guid = get_input('group_guid');

$entity = get_entity($entity_guid);
$group = get_entity($group_guid);

$valid = TRUE;

echo "<pre>";

if (!elgg_instanceof($entity, 'object')) {
	echo "Invalid Entity!\r\n";
	$valid = FALSE;
}

if (!elgg_instanceof($group, 'group')) {
	echo "Invalid Group!\r\n";
	$valid = FALSE;
}

if ($valid) {
	$entity->container_guid = $group->guid;
	$entity->save();
	echo "Moved: $entity_guid\r\n";
	echo "To: $group_guid";
}

echo "</pre>";

forward(REFERER);