<?php
/**
 * Elgg forgotten password.
 *
 * @package Elgg
 * @subpackage Core
 */

if (elgg_is_logged_in()) {
	$user = elgg_get_logged_in_user_entity();
}
?>

<div class="mtm">
	<?php echo elgg_echo('tgsadmin:password:text'); ?>
</div>
<div>
	<label><?php echo elgg_echo('tgsadmin:label:usernameemail'); ?></label><br />
	<?php echo elgg_view('input/text', array('name' => 'username', 'value' => $user->username)); ?>
</div>
<?php echo elgg_view('input/captcha'); ?>
<div class="elgg-foot">
	<?php echo elgg_view('input/submit', array('value' => elgg_echo('request'))); ?>
</div>
<?php //@todo JS 1.8: no ?>
<script type="text/javascript">
	$(document).ready(function() {
		$('input[name=username]').focus();
	});
</script>