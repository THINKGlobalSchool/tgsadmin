<?php
/**
 * Elgg TGSAdmin custom exception script
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2016
 * @link http://www.thinkglobalschool.org
 *
 */

$notify = elgg_get_plugin_setting('fatalemail', 'tgsadmin');

if (!empty($notify)) { 
	// Send mail
	$time = time();
	$type = get_class($exception);
	$message = $exception->getMessage();
	mail($notify, "{$type}: {$time}", "{$last_error['type']} {$message}\r\n" . nl2br(htmlentities(print_r($exception, true), ENT_QUOTES, 'UTF-8')));
}
