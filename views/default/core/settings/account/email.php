<?php
/**
 * Provide a way of setting your 
 * OVERRIDE: Disable the email field for regular users
 *
 * @package Elgg
 * @subpackage Core
 */

$user = elgg_get_page_owner_entity();

if ($user) {
?>
<div class="elgg-module elgg-module-info">
	<div class="elgg-head">
		<h3><?php echo elgg_echo('email:settings'); ?></h3>
	</div>
	<div class="elgg-body">
		<p>
			<?php echo elgg_echo('email:address:label'); ?>:
			<?php
			
				$view_vars = array(
					'name' => 'email', 
					'value' => $user->email,
				);
				
				if (!elgg_is_admin_logged_in()) {
					$view_vars['readonly'] = 'readonly';
					$view_vars['class'] = 'elgg-state-disabled';
				}
	
				echo elgg_view('input/email', $view_vars);
			?>
		</p>
	</div>
</div>
<?php
}