<?php
/**
 * Elgg TGSAdmin plugin settings
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2009-2010
 * @link http://www.thinkglobalschool.com
 */

// set default values
if (!isset($vars['entity']->enable_autofriend)) {
	$vars['entity']->enable_autofriend = 'no';
}

if (!isset($vars['entity']->enable_externallinks)) {
	$vars['entity']->enable_externallinks = 'no';
}

if (!isset($vars['entity']->enable_maintenance)) {
	$vars['entity']->enable_maintenance = 'no';
}

// Autofriend
echo '<div><br />';
echo '<h3>' . elgg_echo('tgsadmin:label:settings:autofriend') . '</h3><br />';
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

// Externallinks
echo '<div>';
echo '<h3>' . elgg_echo('tgsadmin:label:settings:externalsettings') . '</h3><br />';
echo elgg_echo('tgsadmin:label:enableexternallinks');
echo ' ';
echo elgg_view('input/dropdown', array(
		'name' => 'params[enable_externallinks]',
		'options_values' => array(
			'no' => elgg_echo('option:no'),
			'yes' => elgg_echo('option:yes')
			),
		'value' => $vars['entity']->enable_externallinks,
));
echo '</div>';


// Maintenance Mode
echo '<div>';
echo '<h3>' . elgg_echo('tgsadmin:label:settings:maintenancesettings') . '</h3><br />';
echo elgg_echo('tgsadmin:label:enablemaintenance');
echo ' ';
echo elgg_view('input/dropdown', array(
		'name' => 'params[enable_maintenance]',
		'options_values' => array(
			'no' => elgg_echo('option:no'),
			'yes' => elgg_echo('option:yes')
			),
		'value' => $vars['entity']->enable_maintenance,
));
echo '</div>';

echo '<div>';
echo elgg_echo('tgsadmin:label:mmtitle');
echo '<br />';
echo elgg_view('input/text', array(
		'name' => 'params[maintenance_title]',
		'value' => $vars['entity']->maintenance_title,
));
echo '</div>';

echo '<div>';
echo elgg_echo('tgsadmin:label:mmmessage');
echo '<br /> ';
echo elgg_view('input/plaintext', array(
		'name' => 'params[maintenance_message]',
		'value' => $vars['entity']->maintenance_message,
));
echo '</div>';
