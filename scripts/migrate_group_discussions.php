<?php
// Group discussion replies are now a specific annotation type, not comments

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
global $CONFIG;
admin_gatekeeper();

$topics = elgg_get_entities(array(
	'type' => 'object',
	'subtype' => 'groupforumtopic',
	'limit' => 0,
));

foreach ($topics as $topic) {
	$options = array(
		'guid' => $topic->guid,
		'annotation_name' => 'generic_comment'
	);
	echo '<br /><pre>' . $topic->title . "<br />";
	echo 'generic_comment: ';
	$comments = elgg_get_annotations($options);
	echo count($comments) . "<br />";
	echo "group_topic_post: ";
	$options['annotation_name'] = 'group_topic_post';
	$replies = elgg_get_annotations($options);
	echo count($replies) . "<br />";
	
	foreach($comments as $comment) {
		update_annotation($comment->id, 'group_topic_post', $comment->value, $comment->value_type, $comment->owner_guid, $comment->access_id);
	}
	echo "</pre>";
}

