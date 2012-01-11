<?php
        /** Script to remove geotagging data from all entities except blogs/users */
        require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
        global $CONFIG;
        admin_gatekeeper();

        $url = $CONFIG->url . "mod/tgstweaks/remove_geo.php";

        if (get_input('submit')) {
                $entities = elgg_get_entities_from_metadata(array('limit' => 1300,
                                                                  'metadata_name_value_pairs' => array(
                                                                                                        'name' => 'geo:lat',
                                                                                                        'operand' => '!=',
                                                                                                        'value' => '0'
                                                                                                        )
                ));

                $count = 0;
                echo "Entities: <br /><br />";
                foreach ($entities as $entity) {
                        if ($entity->type != 'user' && $entity->getSubtype() != 'blog') {
                                elgg_delete_metadata(array('guid' => $entity->getGUID(), 'metadata_name' => 'geo:lat'));
                                elgg_delete_metadata(array('guid' => $entity->getGUID(), 'metadata_name' => 'geo:long'));
                                echo "Entity: " . $entity->getGUID() . "<br />";
                                echo "Entity Lat: " . $entity->get('geo:lat') . "<br />";
                                echo "Entity Long: " . $entity->get('geo:long') . "<br /><br />";
                                $count++;
                        }
                }
                echo "Updated: " . $count;
        } else {
                echo <<<EOT
                <h2>Clear Geotagging Data</h2>
                <form action='$url' method='POST'>
                        <input type='submit' name='submit' value='Clear Geo' />
                </form>
EOT;
        }

?>
