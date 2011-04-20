<?php
/**
 * Assign/Unassign group select form
 * 
 * @package TGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.org/
 * 
 */

$style = "<style type='text/css'>" . elgg_view('assign/css') . "</style>";
$script = <<<HTML
	<script type='text/javascript'>
			$(document).ready(function() {
				$('#group-picker').change(function() {
					$(this).closest('#group-select-form').submit();
				});
			});
	</script>
HTML;

$entities = get_assign_groups();

$title = elgg_view_title(elgg_echo('tgsadmin:label:unassign'));

$groups_label = elgg_echo('tgsadmin:label:selectgroup');
$groups_input = elgg_view('input/pulldown', array(
												'internalid' => 'group-picker',
												'internalname' => 'entity_guid',
												'options_values' => $entities,
												'value' => get_input('entity_guid', 0),
											));
		
// Form content
$form_body = <<<EOT
	$title<br />
	<label>$groups_label</label><br />
	$groups_input<br />
EOT;

echo $script . $form_body;
