<?php
/**
 * Elgg TGSAdmin plugin settings
 *
 * @package ElggAutoFriend
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2009-2010
 * @link http://www.thinkglobalschool.com
 */

// set default value
if (!isset($vars['entity']->enable_autofriend)) {
	$vars['entity']->enable_autofriend = 'no';
}

echo '<div>';
echo elgg_echo('tgsadmin:label:enableautofriend');
echo ' ';
echo elgg_view('input/dropdown', array(
		'name' => 'params[enable_autofriend]',
		'options_values' => array(
			'no' => elgg_echo('option:no'),
			'yes' => elgg_echo('option:yes')
			),
		'value' => $vars['entity']->enable_autofriend,
));
echo '</div>';
