<?php
/**
 * Elgg TGSAdmin sidebar 
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2009-2010
 * @link http://www.thinkglobalschool.com
 */

// Only display on main activity page
if (elgg_is_logged_in() && elgg_get_context() == 'activity') {
	$user = elgg_get_logged_in_user_entity();
	$limit = 6;
	$groups = get_users_membership($user->getGUID());
	
	$groups_link = elgg_get_site_url(). "groups/all";

	// If we have some groups
	if ($groups) {
		$counter = 0;
		foreach ($groups as $group) {
			if ($counter >= $limit) {
				break;
			}
			$params = array(
				'name' => $group->username,
				'text' => $group->name,
				'href' => $group->url,
			);
			elgg_register_menu_item('tgsgroups', $params);
			$counter++;
		}
		
		$content = elgg_view_menu('tgsgroups', array(
			'sort_by' => 'priority',
			'class' => 'elgg-menu elgg-menu-page elgg-menu-page-default',
		));
		
		// If over the limit
		if (count($groups) > $limit) {
			$content .= "<a href='$groups_link'>More...</a>";
		}
	} else {
		$content = "<a href='$groups_link'>" . elgg_echo('sidebar:groups:createorjoinlink') . "</a> ";
	}

	// Groups module
	echo elgg_view_module('aside', elgg_echo('groups'), $content);
}
