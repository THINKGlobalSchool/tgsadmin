<?php
/** 
 * My initial migrate script didn't create the first reply for topics from the topic 
 * description. This will fix that.
 */
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

$go = get_input('go', FALSE);
$t = get_input('t', FALSE);
$f = get_input('f', FALSE);


echo "<pre>FIX FORUM TOPICS WITHOUT DESCRIPTION<br /><br />";

if ($go) {
	$topic = get_entity($t);
	$forum_topic = get_entity($f);
	
	if (elgg_instanceof($topic, 'object', 'groupforumtopic') && elgg_instanceof($forum_topic, 'object', 'forum_topic')) {
		echo "TOPIC		| GUID: $topic->guid - Name: $topic->title<br />";
		echo "FORUM_TOPIC	| GUID: $forum_topic->guid - Name: $forum_topic->title<br />";
		
		echo "<br />Topic description:<br />$topic->description";
		
		// Create intitial reply from topic description
		$forum_reply = new ElggObject();
		$forum_reply->subtype = 'forum_reply';
		$forum_reply->access_id = $forum_topic->access_id;
		$forum_reply->topic_guid = $forum_topic->guid;
		$forum_reply->container_guid = $forum_topic->container_guid;
		$forum_reply->owner_guid = $topic->owner_guid;
		$forum_reply->description = $topic->description;
		$forum_reply->save();
		$forum_reply->time_created = $forum_topic->time_created;
		$forum_reply->save();

		// Add reply to relationship
		add_entity_relationship($forum_reply->guid, FORUM_REPLY_RELATIONSHIP, $forum_topic->guid);

		echo "<br />Created new reply in forum topic: $forum_reply->description";
	} else {
		echo "<br />INVALID ENTITIES<br />";
	}	

} else {
	$topic_options = array(
		'type' => 'object',
		'subtype' => 'groupforumtopic',
		'limit' => 0,
		'count' => TRUE,
	);

	// Count topics
	$topic_count = elgg_get_entities($topic_options);

	echo "Found $topic_count topic(s)<br /><br />";

	$topic_options['count'] = FALSE;

	// Get topics
	$topics = elgg_get_entities($topic_options);

	// List em
	foreach ($topics as $topic) {
		echo "GUID: $topic->guid - Name: $topic->title<br />";
	}

	$forum_topic_options = array(
		'type' => 'object',
		'subtype' => 'forum_topic',
		'limit' => 0,
		'count' => TRUE,
	);

	// Count forum topics
	$forum_topic_count = elgg_get_entities($forum_topic_options);

	echo "<br />Found $forum_topic_count forum_topic(s)<br /><br />";

	$forum_topic_options['count'] = FALSE;

	// Get forum topics
	$forum_topics = elgg_get_entities($forum_topic_options);

	// List em
	foreach ($forum_topics as $forum_topic) {
		echo "GUID: $forum_topic->guid - Name: $forum_topic->title<br />";
	}

	// Find matchess
	echo "<br />CHECKING FOR MATCHES...<br />";

	$url = elgg_get_site_url();

	foreach($topics as $topic) {
		$match = 0;
		foreach($forum_topics as $forum_topic) {
			// Check for a match, will match if time created is the same and both are
			// contained by the same group
			if ($topic->time_created == $forum_topic->time_created
				&& $topic->container_guid == get_entity($forum_topic->container_guid)->container_guid) {
				$match = 1;
				$container = get_entity($topic->container_guid);
				break;
			}
		}
	
	
		if ($match) {
			echo "<br />Found Match! In group: $container->name:<br />GUID: $topic->guid - $topic->title <br />GUID: $forum_topic->guid - $forum_topic->title<br />";
			echo "<a target='_blank' href='{$url}forums/forum_topic/view/{$forum_topic->guid}'>View Forum Topic</a> - ";
			echo "<a target='_blank' href='{$url}mod/tgsadmin/scripts/fix_forum_topics_without_description.php?go=1&t={$topic->guid}&f={$forum_topic->guid}'>Populate reply!</a><br />";
		}
	}
}

echo "</pre>";