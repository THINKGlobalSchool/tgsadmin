<?php
/**
 * Elgg TGSAdmin plugin settings
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2015
 * @link http://www.thinkglobalschool.com
 */

if (!isset($vars['entity']->enable_externallinks)) {
	$vars['entity']->enable_externallinks = 'no';
}

if (!isset($vars['entity']->enable_execution_logging)) {
	$vars['entity']->enable_execution_logging = 'no';
}

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

$cronsettings_heading = elgg_echo('tgsadmin:label:cronsettings');
$cron_query_key_label = elgg_echo('tgsadmin:label:cronquerykey');
$cron_query_key_input = elgg_view('input/text', array(
	'name' => 'params[cronquerykey]',
	'value' => $vars['entity']->cronquerykey,
));

$content = <<<HTML
	<h3>$externalsettings_heading</h3><br />
	<div>
		<div>
			<label>$externallinks_label</label>
			$externallinks_input
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
	<h3>$cronsettings_heading</h3><br />
	<div>
		<div>
			<label>$cron_query_key_label</label>
			$cron_query_key_input
		</div>
	</div>
HTML;

echo $content;

