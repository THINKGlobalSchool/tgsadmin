<?php
/**
 * Elgg exception (failsafe mode)
 * Displays a single exception
 *
 * @package Elgg
 * @subpackage Core
 *
 * @uses $vars['object'] An exception
 */

?>

<p class="messages_exception">
	<span title="<?php echo get_class($vars['object']); ?>">
	<?php

		echo nl2br($vars['object']->getMessage());

	?>
	</span>
</p>