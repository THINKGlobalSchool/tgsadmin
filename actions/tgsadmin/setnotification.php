<?php
/**
 * Elgg TGSAdmin Set notification setting action
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2012
 * @link http://www.thinkglobalschool.com
 *
 */

$user_guid = get_input('user_guid');
$value = get_input('checked');
$method = get_input('method');

$user = get_entity($user_guid);

if (!elgg_instanceof($user, 'user')) {
	register_error(elgg_echo('tgsadmin:error:invaliduser'));
	forward(REFERER);
}

if (set_user_notification_setting($user_guid, $method, ($value == 'yes') ? true : false)) {
	system_message(elgg_echo('tgsadmin:success:usernotification'));
} else {
	register_error(elgg_echo('tgsadmin:error:usernotification'));
}

forward(REFERER);