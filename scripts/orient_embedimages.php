<?php
/** Fix unhidden roles **/
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
global $CONFIG;
admin_gatekeeper();

echo "<h1>AUTO-ORIENT EMBEDIMAGES</h1><pre>";

$prefix = "file/";

if (get_input('orient')) {
	$file = new FilePluginFile(get_input('orient'));
	if (!$file) {
		echo "Invalid file";
	} else {
		$filename = $prefix . substr($file->getFilename(), 5);
		$thumbname = $prefix . 'thumb' . substr($file->getFilename(), 5);
		$smallthumbname = $prefix . 'smallthumb' . substr($file->getFilename(), 5);
		$largethumbname = $prefix . 'largethumb' . substr($file->getFilename(), 5);
	
		// Being SUPER careful here to make sure I've got the right file names
		$filematch = ($filename == $file->getFilename());
		$thumbmatch = ($thumbname == $file->thumbnail);
		$smallthumbmatch = ($smallthumbname == $file->smallthumb);
		$largethumbmatch = ($largethumbname == $file->largethumb);
	
		echo "{$file->guid}:<br>{$filename} ($filematch)<br>{$thumbname} ($thumbmatch)<br>{$smallthumbname} ($smallthumbmatch)<br>{$largethumbname} ($largethumbmatch)<br><br>";
		
		if ($filematch && $thumbmatch && $smallthumbmatch && $largethumbmatch && get_input('go')) {
			// Auto-orient the main image
			$command = elgg_get_plugin_setting('im_path', 'tidypics') . "convert \"" . $file->getFilenameOnFilestore() . "\" -auto-orient \"" . $file->getFilenameOnFilestore() . "\"";
			exec($command);
		
			$thumbnail = get_resized_image_from_existing_file($file->getFilenameOnFilestore(),60,60, true);
			if ($thumbnail) {
				echo "creating thumb<br>";
				$thumb = new ElggFile();
				$thumb->setMimeType($file->getMimeType());

				$thumb->setFilename($thumbname);
				$thumb->open("write");
				$thumb->write($thumbnail);
				$thumb->close();

				$file->thumbnail = $thumbname;
				unset($thumbnail);
			}

			$thumbsmall = get_resized_image_from_existing_file($file->getFilenameOnFilestore(),153,153, true);
			if ($thumbsmall) {
				echo "creating smallthumb<br>";
				$thumb->setFilename($smallthumbname);
				$thumb->open("write");
				$thumb->write($thumbsmall);
				$thumb->close();
				$file->smallthumb = $smallthumbname;
				unset($thumbsmall);
			}

			$thumblarge = get_resized_image_from_existing_file($file->getFilenameOnFilestore(),600,600, false);
			if ($thumblarge) {
				echo "creating largethumb<br>";
				$thumb->setFilename($largethumbname);
				$thumb->open("write");
				$thumb->write($thumblarge);
				$thumb->close();
				$file->largethumb = $largethumbname;
				unset($thumblarge);
			}
			
			echo "<br>done!";
		}
	}
} else {
	$options = array(
		'type' => 'object',
		'subtype' => 'embedimage',
		'limit' => 0,
	);

	$images = elgg_get_entities($options);

	foreach ($images as $image) {
	
		$file = new FilePluginFile($image->guid);

		$filename = $prefix . substr($file->getFilename(), 5);
		$thumbname = $prefix . 'thumb' . substr($file->getFilename(), 5);
		$smallthumbname = $prefix . 'smallthumb' . substr($file->getFilename(), 5);
		$largethumbname = $prefix . 'largethumb' . substr($file->getFilename(), 5);
	
		// Being SUPER careful here to make sure I've got the right file names
		$filematch = ($filename == $file->getFilename());
		$thumbmatch = ($thumbname == $file->thumbnail);
		$smallthumbmatch = ($smallthumbname == $file->smallthumb);
		$largethumbmatch = ($largethumbname == $file->largethumb);
	
		echo "{$file->guid}:<br>{$filename} ($filematch)<br>{$thumbname} ($thumbmatch)<br>{$smallthumbname} ($smallthumbmatch)<br>{$largethumbname} ($largethumbmatch)<br><br>";
	
		// We've got the right filenames.. start re-creating
		if ($filematch && $thumbmatch && $smallthumbmatch && $largethumbmatch && get_input('go')) {
			// Auto-orient the main image
			$command = elgg_get_plugin_setting('im_path', 'tidypics') . "convert \"" . $file->getFilenameOnFilestore() . "\" -auto-orient \"" . $file->getFilenameOnFilestore() . "\"";
			exec($command);
		
			$thumbnail = get_resized_image_from_existing_file($file->getFilenameOnFilestore(),60,60, true);
			if ($thumbnail) {
				$thumb = new ElggFile();
				$thumb->setMimeType($file->getMimeType());

				$thumb->setFilename($thumbname);
				$thumb->open("write");
				$thumb->write($thumbnail);
				$thumb->close();

				$file->thumbnail = $thumbname;
				unset($thumbnail);
			}

			$thumbsmall = get_resized_image_from_existing_file($file->getFilenameOnFilestore(),153,153, true);
			if ($thumbsmall) {
				$thumb->setFilename($smallthumbname);
				$thumb->open("write");
				$thumb->write($thumbsmall);
				$thumb->close();
				$file->smallthumb = $smallthumbname;
				unset($thumbsmall);
			}

			$thumblarge = get_resized_image_from_existing_file($file->getFilenameOnFilestore(),600,600, false);
			if ($thumblarge) {
				$thumb->setFilename($largethumbname);
				$thumb->open("write");
				$thumb->write($thumblarge);
				$thumb->close();
				$file->largethumb = $largethumbname;
				unset($thumblarge);
			}
		}
	}
}