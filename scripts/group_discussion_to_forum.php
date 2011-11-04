<?php
/** 
 * Migrate Group Discussions to Group Forum 
 *
 * Handle:
 *  - Activity (River items)
 *  - Create new forum (generic title)
 *  - Disable group discussions
 *  - Update existing tag dashboards
 */
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

$go = get_input('go', FALSE);
$guid = get_input('guid', NULL);

echo "<pre>MIGRATE GROUP DISCUSSIONS TO GROUP FORUM<br /><br />";

$options = array(
	'type' => 'group',
	'limit' => 0,
	'guid' => $guid,
);

$groups = new ElggBatch('elgg_get_entities', $options);

echo "GROUPS:<br/>-------<br />";

// Check each group for disussions and migrate when required
foreach ($groups as $group) {
	
	$options = array(
		'type' => 'object',
		'subtype' => 'groupforumtopic',
		'limit' => 0,
		'count' => TRUE,
		'container_guid' => $group->guid,
	);
	
	// Count topics
	$topic_count = elgg_get_entities($options);
	
	$options['count'] = FALSE;
	
	// Get topics
	$topics = new ElggBatch('elgg_get_entities', $options);
	
	echo "<br />GUID: $group->guid - Name: $group->name - Topics: $topic_count<br /><br />";

	// Only execute if we have topics
	if ($go && $topic_count > 0) { 
		// Create a new group forum
		$forum = new ElggObject();
		$forum->subtype = 'forum';
		$forum->container_guid = $group->guid;
		$forum->owner_guid = $group->owner_guid;
		$forum->site_forum = FALSE;
		$forum->title = $group->name . " forum";
		$forum->anonymous = FALSE;
		$forum->moderator_role = NULL;
		$forum->access_id = ACCESS_LOGGED_IN; // Set logged in.. group owner can adjust later
		$forum->save();
	}
	
	// Disable group discussions
	$group->forum_enable = 'no';
	
	// Make sure forums are enabled
	$group->forums_enable = 'yes';

	foreach ($topics as $topic) {
		// Count replies
		$reply_count = $topic->countAnnotations('group_topic_post', 'count');
		
		$options = array(
			'guid' => $topic->getGUID(),
			'annotation_name' => 'group_topic_post',
		);

		// Get replies
		$replies = elgg_get_annotations($options);
		
		echo "Topic -> GUID: $topic->guid - Replies: $reply_count - Title: $topic->title<br />";

		if ($go && elgg_instanceof($forum, 'object', 'forum')) {
			// Create new topic in forum
			$forum_topic = new ElggObject();
			$forum_topic->subtype = 'forum_topic';
			$forum_topic->access_id = $forum->access_id;
			$forum_topic->container_guid = $forum->guid;
			$forum_topic->owner_guid = $topic->owner_guid;
			$forum_topic->title = $topic->title;
			$forum_topic->tags = $topic->tags;
			$forum_topic->save();
			
			// This is stupid.. need to 'double tap save' to set a new time_created
			$forum_topic->time_created = $topic->time_created; 
			$forum_topic->save();		
	
			$nt = get_entity($forum_topic->guid);
			
			// Add topic river entry
			add_to_river(
				'river/object/forum_topic/create', 
				'create', 
				$forum_topic->owner_guid, 
				$forum_topic->guid, 
				"", 
				$topic->time_created
			);
			
						
			// Delete river entries for this topic, including replies
			elgg_delete_river(array(
				'object_guid' => $topic->guid,
			));
			
			
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
			
			// Add reply river entry
			add_to_river(
				'river/object/forum_reply/create', 
				'create', 
				$forum_reply->owner_guid, 
				$forum_reply->guid,
				"",
				$reply->time_created
			);
		}
		
		foreach ($replies as $reply) {			
			echo "	Reply ID: $reply->id<br />";

			if ($go && elgg_instanceof($forum, 'object', 'forum') && elgg_instanceof($forum_topic, 'object', 'forum_topic')) {
				// Create new reply to topic
				$forum_reply = new ElggObject();
				$forum_reply->subtype = 'forum_reply';
				$forum_reply->access_id = $forum_topic->access_id;
				$forum_reply->topic_guid = $forum_topic->guid;
				$forum_reply->container_guid = $forum_topic->container_guid;
				$forum_reply->owner_guid = $reply->owner_guid;
				$forum_reply->description = $reply->value;
				$forum_reply->save();
				
				// Annoying..
				$forum_reply->time_created = $reply->time_created;
				$forum_reply->save();

				// Add reply to relationship
				add_entity_relationship($forum_reply->guid, FORUM_REPLY_RELATIONSHIP, $forum_topic->guid);
				
				// Add reply river entry
				add_to_river(
					'river/object/forum_reply/create', 
					'create', 
					$forum_reply->owner_guid, 
					$forum_reply->guid,
					"",
					$reply->time_created
				);
			}
		}	
		echo "<br />";
	}
}

// Fix existing tag dashboards
if (get_input('updatetd', FALSE)) {
	$subtypes = tagdashboards_get_enabled_subtypes();

	// Disable documents and enable files in settings
	foreach ($subtypes as $idx => $type) {
		if ($type == 'groupforumtopic') {
			unset($subtypes[$idx]);
			$subtypes[] = 'forum_topic';
		}
	}

	// Grab all tagdashboards
	$options = array(
		'type' => 'object',
		'subtype' => 'tagdashboard',
		'limit' => 0,
	);

	$tagdashboards = new ElggBatch('elgg_get_entities', $options);

	// Disable groupforumtopic and enable forum_topic for all dashboards
	foreach ($tagdashboards as $dashboard) {
		$subtypes = unserialize($dashboard->subtypes);
		foreach ($subtypes as $idx => $type) {
			if ($type == 'groupforumtopic') {
				unset($subtypes[$idx]);
				$subtypes[] = 'forum_topic';
				$dashboard->subtypes = serialize($subtypes);
				$dashboard->save();
			}
		}
	}
}

elgg_set_plugin_setting('enabled_subtypes', serialize($subtypes), 'tagdashboards');


if (!$go) {
	$url = elgg_get_site_url();
	echo "<br /><form action='{$url}mod/tgsadmin/scripts/group_discussion_to_forum.php'>";
	echo "<input type='submit' name='go' value='GO' />";
	echo "<input type='hidden' name='guid' value='$guid' />";
	echo "</form>";	
}

echo "</pre>";