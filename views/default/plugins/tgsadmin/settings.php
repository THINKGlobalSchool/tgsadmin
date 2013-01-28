<?php
/**
 * Elgg TGSAdmin plugin settings
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2012
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

if (!isset($vars['entity']->enable_execution_logging)) {
	$vars['entity']->enable_execution_logging = 'no';
}

$autofriend_heading = elgg_echo('tgsadmin:label:settings:autofriend');
$autofriend_label = elgg_echo('tgsadmin:label:enableautofriend');
$autofriend_input = elgg_view('input/dropdown', array(
	'name' => 'params[enable_autofriend]',
	'options_values' => array(
		'no' => elgg_echo('option:no'),
		'yes' => elgg_echo('option:yes')
		),
	'value' => $vars['entity']->enable_autofriend,
));

$externalsettings_heading = elgg_echo('tgsadmin:label:settings:externalsettings');
$externallinks_label = elgg_echo('tgsadmin:label:enableexternallinks');
$externallinks_input = elgg_view('input/dropdown', array(
	'name' => 'params[enable_externallinks]',
	'options_values' => array(
		'no' => elgg_echo('option:no'),
		'yes' => elgg_echo('option:yes')
		),
	'value' => $vars['entity']->enable_externallinks,
));

$maintenancemode_heading = elgg_echo('tgsadmin:label:settings:maintenancesettings');
$maintenancemode_enable_label = elgg_echo('tgsadmin:label:enablemaintenance');
$maintenancemode_enable_input = elgg_view('input/dropdown', array(
	'name' => 'params[enable_maintenance]',
	'options_values' => array(
		'no' => elgg_echo('option:no'),
		'yes' => elgg_echo('option:yes')
		),
	'value' => $vars['entity']->enable_maintenance,
));

$maintenancemode_title_label = elgg_echo('tgsadmin:label:mmtitle');
$maintenancemode_title_input = elgg_view('input/text', array(
	'name' => 'params[maintenance_title]',
	'value' => $vars['entity']->maintenance_title,
));

$maintenancemode_message_label = elgg_echo('tgsadmin:label:mmmessage');
$maintenancemode_message_input = elgg_view('input/plaintext', array(
	'name' => 'params[maintenance_message]',
	'value' => $vars['entity']->maintenance_message,
));

$emailsettings_heading = elgg_echo('tgsadmin:label:emailsettings');
$site_noreply_label = elgg_echo('tgsadmin:label:noreplyemail');
$site_noreply_input = elgg_view('input/text', array(
	'name' => 'params[noreplyemail]',
	'value' => $vars['entity']->noreplyemail,
));

$loggingsettings_heading = elgg_echo('tgsadmin:label:loggingsettings');
$logexecution_enable_label = elgg_echo('tgsadmin:label:enableexecution');
$logexecution_enable_input = elgg_view('input/dropdown', array(
	'name' => 'params[enable_execution_logging]',
	'options_values' => array(
		'no' => elgg_echo('option:no'),
		'yes' => elgg_echo('option:yes')
		),
	'value' => $vars['entity']->enable_execution_logging,
));

$content = <<<HTML
	<h3>$autofriend_heading</h3><br />
	<div>
		<div>
			<label>$autofriend_label</label>
			$autofriend_input
		</div>
	</div>
	<h3>$externalsettings_heading</h3><br />
	<div>
		<div>
			<label>$externallinks_label</label>
			$externallinks_input
		</div>
	</div>
	<h3>$maintenancemode_heading</h3><br />
	<div>
		<div>
			<label>$maintenancemode_enable_label</label>
			$maintenancemode_enable_input
		</div><br />
		<div>
			<label>$maintenancemode_title_label</label></br>
			$maintenancemode_title_input
		</div><br />
		<div>
			<label>$maintenancemode_message_label</label></br>
			$maintenancemode_message_input
		</div>
	</div>
	<h3>$emailsettings_heading</h3><br />
	<div>
		<div>
			<label>$site_noreply_label</label>
			$site_noreply_input
		</div>
	</div>
	<h3>$loggingsettings_heading</h3><br />
	<div>
		<div>
			<label>$logexecution_enable_label</label>
			$logexecution_enable_input
		</div>
	</div>
HTML;

echo $content;

