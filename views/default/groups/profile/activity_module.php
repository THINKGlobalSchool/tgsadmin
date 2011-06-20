<?php
/**
 * TGSAdmin groups activity module override
 * - Displays the add wire post form in the group activity module
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2009-2010
 * @link http://www.thinkglobalschool.com
 *
 */

if ($vars['entity']->activity_enable == 'no') {
	return true;
}

$group = $vars['entity'];
if (!$group) {
	return true;
}

$all_link = elgg_view('output/url', array(
	'href' => "groups/activity/$group->guid",
	'text' => elgg_echo('link:view:all'),
));

$header = "<span class=\"groups-widget-viewall\">$all_link</span>";
$header .= '<h3>' . elgg_echo('groups:activity') . '</h3>';

$content = '';

if (elgg_get_plugin_setting('post_from_activity_stream', 'tgsadmin') == 'yes' && elgg_is_logged_in()) {
	$content = elgg_view('tweaks/wire_form', array('group' => $vars['entity']));
}

elgg_push_context('widgets');
$db_prefix = elgg_get_config('dbprefix');
$content .= elgg_list_river(array(
	'limit' => 4,
	'pagination' => false,
	'joins' => array("join {$db_prefix}entities e1 on e1.guid = rv.object_guid"),
	'wheres' => array("(e1.container_guid = $group->guid)"),
));
elgg_pop_context();

if (!$content) {
	$content = '<p>' . elgg_echo('groups:activity:none') . '</p>';
}

echo elgg_view_module('info', '', $content, array('header' => $header));
