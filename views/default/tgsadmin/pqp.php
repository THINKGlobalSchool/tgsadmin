<?php
/**
 * Elgg TGSAdmin PQP Footer
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2012
 * @link http://www.thinkglobalschool.com
 *
 */
if (get_input('pqp_profiler') == 'pqp_profile') {
	$profiler = elgg_get_config('pqp_profiler');
	
	$db = elgg_get_config('pqp_db');
	
	//var_dump($db);
	$profiler->display($db);
}
?>