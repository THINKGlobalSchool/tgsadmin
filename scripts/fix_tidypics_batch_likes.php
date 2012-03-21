<?php
/** Remove duplicate todo submissions **/
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
global $CONFIG;
admin_gatekeeper();

echo "<h1>FIX TIDYPICS BATCH LIKES</h1><pre>";

$options = array(
	'type' => 'object',
	'subtype' => 'tidypics_batch',
	'limit' => 0,
);

$batches = new ElggBatch('elgg_get_entities', $options);

foreach ($batches as $batch) {
	echo "\r\nBATCH: $batch->guid\r\n";		
	$likes = elgg_get_annotations(array(
		'guid' => $batch->guid,
		//'annotation_owner_guid' => $user->guid,
		'annotation_name' => 'likes',
	));
	foreach ($likes as $like) {
		$owner = $like->getOwnerEntity();
		echo "\r\n	Liked: $owner->username\r\n";
		echo "	---------------------------------\r\n";

		if (get_input('go')) {
			// Get batch images
			$images = elgg_get_entities_from_relationship(array(
				'relationship' => 'belongs_to_batch',
				'relationship_guid' => $batch->getGUID(),
				'inverse_relationship' => true,
				'types' => array('object'),
				'subtypes' => array('image'),
				'limit' => 999,
				'offset' => 0,
				'count' => false,
			));

			// Like each image in the batch
			foreach ($images as $image) {
				// Make sure we haven't liked already
				if (!elgg_annotation_exists($image->guid, 'likes', $owner->guid)) {
					echo "	Like -> $image->guid\r\n";
					$annotation = create_annotation($image->guid,'likes',"likes","",$owner->guid,$image->access_id);
				}
			}
			
			// Remove Empty album comments
			$empty_comments = elgg_get_annotations(array(
				'guid' => $batch->container_guid,
				'annotation_owner_guid' => $owner->guid,
				'annotation_name' => 'generic_comment',
			));
			
			foreach($empty_comments as $ec) {
				if (empty($ec->value)) {
					echo "\r\n	Empty Album ($batch->container_guid) Comment - $ec->id\r\n";
					$ec->delete();
				}
			}
		}
	}
}

echo "</pre>";