<?php
/**
 * Elgg TGSAdmin add groups to dashboard sidebar
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2009-2010
 * @link http://www.thinkglobalschool.com
 */

if (elgg_is_logged_in()) {
	$user = elgg_get_logged_in_user_entity();
	$limit = 50;
	$groups = get_users_membership($user->getGUID());
	
	$content = '<ul class="submenu access_collections">';

	foreach ($groups as $group) {
		//if ($collection->getGUID() == $sac_guid) {
		//	$selected = 'selected';
		//} else {
		//	$selected = '';
		//}
		$content .= <<<___END
		<li class="thewire_shared_access $selected">
			<a href="{$group->getURL()}">{$group->name}</a>
		</li>
___END;
	}

	if (count($groups) > $limit) {
		$content .= <<<___END
		<li class="thewire_shared_access more">
			<a href="{$vars['url']}pg/groups">More...</a>
		</li>
___END;
	}
	$content .= '</ul>';
?>

<h3 class="groups_title"><?php echo elgg_echo('groups'); ?></h3>
<?php 
	if($groups)
		echo $content; 
	else
		echo "<a href=\"{$vars['url']}pg/groups/world\">". elgg_echo('sidebar:groups:createorjoinlink') . "</a> ";
}//end of opening if statement
?>
