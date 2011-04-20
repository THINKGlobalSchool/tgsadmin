<?php
/**
 * Assign/Unassign Members list
 * 
 * @package TGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.org/
 * 
 */

$entity = get_entity($vars['guid']);

$options = array(
	'relationship_guid' => $entity->getGUID(),
	'inverse_relationship' => TRUE,
	'limit' => 0, 
);

if (elgg_instanceof($entity, 'group')) {
	$options['relationship'] = 'member';
} else if ($entity->getSubtype() == 'shared_access') {
	$options['relationship'] = 'shared_access_member';
}

$users = elgg_get_entities_from_relationship($options);

foreach ($users as $user) {
	$name = $user->name;
	
	$href = elgg_get_site_url() . "action/tgsadmin/unassign?user=" . $user->getGUID() . "&e=" . $entity->getGUID();
	
	$remove_link = elgg_view('output/confirmlink', array(
		'href' => $href,
		'text' => elgg_echo('tgsadmin:label:remove'),
		'class' => 'elgg-button'
	));

	$content .= <<<HTML
		<div class='assign-user-listing'>
			<div class='assign-user-action'>
				$remove_link
			</div>
			<div class='assign-user-name'>
				<h4>$name<h4>
			</div>
			<div style='clear: both;'></div>
		</div>
HTML;
}

echo $content;