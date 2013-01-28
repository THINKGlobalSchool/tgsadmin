<?php
/**
 * TGS Admin stats
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 *
 */
?>
<!-- ADD STUFF TO TOPBAR HERE -->
<?php 
	if (elgg_is_admin_logged_in() || get_input('show_execution')) {
		$foot_time = tgsadmin_get_execution_time();
?>
	<div class='tgsadmin-stats'>
		<?php
			if (!elgg_in_context('admin')) {
				?>
				<strong>Topbar Time: <span id='tgsadmin-topbar-ready'> ... </span> | </strong>
				<?php
			}
		?>
		<strong>Script Execution Time: <span id='tgsadmin-script-execution'><?php echo $foot_time; ?> seconds </span></strong>
	</div>
	<script type='text/javascript'>
		$(document).ready(function() {
			$("#tgsadmin-topbar-ready").html($('#tgsadmin-topbar-ready-time').html());
		});
	</script>
<?php
	}