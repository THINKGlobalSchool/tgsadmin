<?php
/**
 * Elgg TGSAdmin lib
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2009-2010
 * @link http://www.thinkglobalschool.com
 *
 */

// Maintenance Mode uses this exception
class MaintenanceModeException extends Exception {}

/** Get admin page content **/
function assign_get_admin_content() {
	$content = elgg_view('assign/forms/assign_form');
	return $content;
}

/** 
 * Return an array suitable for a dropdown of 
 * all groups and, if enabled, channels
 * 
 * @return array
 */
function get_assign_groups() {
	$groups = elgg_get_entities(array('types' => 'group', 'limit' => 9999));
			
	if (elgg_is_active_plugin('shared_access')) {
	
		$channels = elgg_get_entities(array('types' => 'object',
											'subtypes' => 'shared_access',
											'limit' => 9999
									  		));
		$groups = array_merge($groups, $channels);
	}
	
	$dropdown = array();
	$dropdown[0] = 'Select..';

	foreach ($groups as $group) {
		$dropdown[$group->getGUID()] = $group->title ? "Channel: " . $group->title : "Group: " . $group->name;
	}
	
	return $dropdown;
}

/**
 * Create a new wire post, the TGS way
 *
 * @param string $text           The post text
 * @param int    $userid         The user's guid
 * @param int    $access_id      Public/private etc
 * @param int    $parent_guid    Parent post guid (if any)
 * @param string $method         The method (default: 'site')
 * @param int    $container_guid Container guid (for group wire posts)
 * @return guid or false if failure
 */
function tgswire_save_post($text, $userid, $access_id, $parent_guid = 0, $method = "site", $container_guid = NULL) {
	$post = new ElggObject();

	$post->subtype = "thewire";
	$post->owner_guid = $userid;
	$post->access_id = $access_id;

	// Check if we're removing the limit
	if (elgg_get_plugin_setting('limit_wire_chars', 'tgsadmin') == 'yes') {
		// only 200 characters allowed
		$text = elgg_substr($text, 0, 200);
	}

	// If supplied with a container_guid, use it
	if ($container_guid) {
		$post->container_guid = $container_guid;
	}

	// no html tags allowed so we escape
	$post->description = htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');

	$post->method = $method; //method: site, email, api, ...

	$tags = thewire_get_hashtags($text);
	if ($tags) {
		$post->tags = $tags;
	}

	// must do this before saving so notifications pick up that this is a reply
	if ($parent_guid) {
		$post->reply = true;
	}

	$guid = $post->save();

	// set thread guid
	if ($parent_guid) {
		$post->addRelationship($parent_guid, 'parent');
		
		// name conversation threads by guid of first post (works even if first post deleted)
		$parent_post = get_entity($parent_guid);
		$post->wire_thread = $parent_post->wire_thread;
	} else {
		// first post in this thread
		$post->wire_thread = $guid;
	}

	if ($guid) {
		add_to_river('river/object/thewire/create', 'create', $post->owner_guid, $post->guid);
	}
	
	return $guid;
}