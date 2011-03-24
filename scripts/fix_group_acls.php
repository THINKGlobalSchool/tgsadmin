<?php
        /** Script to make sure groups users are in the proper ACL **/
        require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
        global $CONFIG;
        admin_gatekeeper();

        $url = $CONFIG->url . "mod/tgstweaks/fix_group_acls.php";

		$entities = elgg_get_entities(array('type' => 'group', 'limit' => 0));
		

        if (get_input('submit')) {
                $count = 0;
                echo "<h1>Groups:</h1>";
                foreach ($entities as $entity) {
					$group_members = $entity->getMembers(0);
					$group_members_string = '';
					foreach ($group_members as $group_member) {
						$group_members_string .= $group_member->name;
						
						try {
							$query = "insert into {$CONFIG->dbprefix}access_collection_membership"
								. " set access_collection_id = {$entity->group_acl}, user_guid = {$group_member->getGUID()}";
							insert_data($query);
							$success = true;

						} catch (DatabaseException $e) {
							$success = false;
						}
						
						if ($success) {
							$group_members_string .= "-->";
						}
						$group_members_string .= "<br />";
						
					} 
					
					$acl_members = get_members_of_access_collection($entity->group_acl);
					
					$acl_members_string = '';
					foreach ($acl_members as $acl_member) {
						$acl_members_string .= $acl_member->name . "<br />";
					}
					
                    echo "<h4>{$entity->name}: {$entity->group_acl}</h4>";
                    echo "<table>
							<tr>
								<td style='width: 150px; vertical-align:top;'>$group_members_string</td>
								<td style='width: 150px; vertical-align:top;'>$acl_members_string</td>
							</tr>
						</table>";
					
					$count++;
                }
                echo "<br />Updated: " . $count;
        } else {
                echo <<<EOT
                <h2>Fix Group ACLS</h2>
                <form action='$url' method='POST'>
                        <input type='submit' name='submit' value="Fix 'Em" />
                </form>
EOT;
        }

?>
