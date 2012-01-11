<?php
        /** Script to remove geotagging data from all entities except blogs/users */
        require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
                include dirname(dirname(dirname(__FILE__))) . "/mod/tidypics/lib/exif.php";
        global $CONFIG;
        admin_gatekeeper();

        $url = $CONFIG->url . "mod/tgstweaks/photos_set_location.php";

        if (get_input('submit')) {
                $entities = elgg_get_entities(array('limit' => 2000,
                                                    'types' => array('object'),
                                                    'subtypes' => array('image')


                                                                                        ));
                $count = 0;
                echo "Entities: <br /><br />";

                $entities_no_data = array();
                // Find all entities without geolocation data
                foreach ($entities as $entity) {
                        $found = false;
                        $metadata =  elgg_get_metadata(array('guid' => $entity->guid, 'limit' => 0));
                        foreach ($metadata as $meta_object) {
                                if ($meta_object->name == 'geo:lat') {
                                        $found = true;
                                }
                        }

                        if (!$found) {
                                $entities_no_data[] = $entity;
                        }
                }

                foreach($entities_no_data as $entity) {
                        // Set latitude/longitude 
                        $exif_data = tp_exif_formatted($entity->getGUID());
                        if ($exif_data['Latitude'] != 'N/A' && $exif_data['Longitude'] != 'N/A') {
                                if (!get_metadata_byname($entity->getGUID(), 'geo:lat') && !get_metadata_byname($entity->getGUID(), 'geo:long')) {
                                        $entity->setLatLong($exif_data['Latitude'],$exif_data['Longitude']);
                                        $count++;
                                }
                        }
                }
                echo "Updated: " . $count;
        } else {
                echo <<<EOT
				                <h2>Set existing photo geolocation data</h2>
				                <form action='$url' method='POST'>
				                        <input type='submit' name='submit' value='Set Location Data' />
				                </form>
EOT;
		}

?>
				