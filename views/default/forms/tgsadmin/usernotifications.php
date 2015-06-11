<?php
/**
 * Elgg TGSAdmin User Notifications Info
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2012
 * @link http://www.thinkglobalschool.com
 * 
 */

admin_gatekeeper();

$NOTIFICATION_HANDLERS = _elgg_services()->notifications->getMethodsAsDeprecatedGlobal();

$options = array(
	'type' => 'user',
	'limit' => 0,
);

$users = new ElggBatch('elgg_get_entities', $options);

$content = '<table style="width: 60%;" class="elgg-table" id="notificationstable"><thead><tr><th>User</th>';

foreach($NOTIFICATION_HANDLERS as $method => $foo) {
	$method_name = elgg_echo('notification:method:'. $method);

	$content .= "<th width='10%'>$method_name</th>";
}

$content .= "</tr></thead><tbody>";

foreach ($users as $user) {
	$content .= "<tr><td class='namefield'><p>{$user->name}</p></td>";

	// Fields

	$fields = '';
	$i = 0;
	foreach($NOTIFICATION_HANDLERS as $method => $foo) {
		if ($notification_settings = get_user_notification_settings($user->guid)) {
			if ($notification_settings->$method) {
				$checked = 'checked="checked"';
			} else {
				$checked = '';
			}
		}

		$content .= <<< HTML
			<td>

			<input class='tgsadmin-user-notification-checkbox' type="checkbox" name="{$user->guid}" id="{$method}checkbox" value="{$method}" {$checked} /></td>
HTML;
		$i++;
	}
	$content .= $fields;

	$content .= "</tr>";
}

$content .= "</tbody></table>";

echo $content;
