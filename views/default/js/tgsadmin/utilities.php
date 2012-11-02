<?php
/**
 * Elgg TGSAdmin utility JS
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2012
 * @link http://www.thinkglobalschool.com
 *
 */
?>
//<script>
elgg.provide('elgg.tgsadmin_utilities');

// Init 
elgg.tgsadmin_utilities.init = function() {
	// Delegate click handler for admin 'move entity'
	$(document).delegate('.tgsadmin-move-button', 'click', elgg.tgsadmin_utilities.moveClick);
}

// Click handler for admin 'move entity'
elgg.tgsadmin_utilities.moveClick = function(event) {
	$('#tgsadmin-utility-output').html("<div class='elgg-ajax-loader'></div>");
	
	var entity_guid = $('input[name=entity_guid]').val();
	var group_guid = $('input[name=group_guid]').val();
	
	// Fire remote copy action
	elgg.action('tgsadmin/move_entity', {
		data: {
			entity_guid: entity_guid,
			group_guid: group_guid,
		},
		success: function(data) {
			if (data.status != -1) {
				var content = '';
				if (data.output) {
					content = data.output;
				} else {
					content = elgg.echo('tgsadmin:error:nodata');
				}
			} else {
				content = data.system_messages.error;
			}
			$('#tgsadmin-utility-output').html(content);
		}
	});

	event.preventDefault();
}

elgg.register_hook_handler('init', 'system', elgg.tgsadmin_utilities.init);
