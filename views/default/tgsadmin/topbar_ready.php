<?php
/**
 * TGS Admin system ready extender
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 *
 */
if (elgg_is_admin_logged_in() || get_input('show_execution')) {
	echo "<div class='hidden' id='tgsadmin-topbar-ready-time'>" . tgsadmin_get_execution_time() . " seconds</div>";
}