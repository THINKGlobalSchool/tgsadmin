<?php
/**
 * Elgg TGSAdmin messages next/previous navigation
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2009-2010
 * @link http://www.thinkglobalschool.com
 *
 */

$full = elgg_extract('full', $vars, false);

if ($full) {
	$owner = elgg_get_page_owner_entity();

	$message_count = elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'messages',
		'metadata_name' => 'toId',
		'metadata_value' => $owner->getGUID(),
		'owner_guid' => $owner->guid,
		'count' => true
	));

	$messages = elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'messages',
		'metadata_name' => 'toId',
		'metadata_value' => $owner->getGUID(),
		'owner_guid' => $owner->guid,
		'limit' => 9999
	));

	$guids = array();

	foreach ($messages as $message) {
		$guids[] = $message->getGUID();
	}
	
	$current = array_search($vars['entity']->getGUID(), $guids);

	if ($current != 0) {
		$next = '<a class="action_button" href="' .$vars['url'] . 'messages/read/' . $guids[$current-1] . '">Next >></a>';
	}

	if ($message_count > $current + 1) {
		$previous = '<a class="action_button" href="' . $vars['url'] . 'messages/read/' . $guids[$current+1] . '"><< Previous</a>';
	}
?>
<br /><br />
<center>
<?php 
		echo $previous . ' ' . $next; 
?>
</center>
<?php
}
?>
