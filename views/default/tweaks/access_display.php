<?php
/**
 * Elgg TGSAdmin channel display content
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2010 - 2015
 * @link http://www.thinkglobalschool.org
 *
 */

$item = $vars['item'];

switch ($item->access_id) {
	case -1: 
		$content = 'Default';
		break;
	case 0: 
		$content = 'Private';
		break;
	case 1: 
		$content = elgg_echo('LOGGED_IN');
		break;
	case 2:
		$content = 'Public';
		break;
	case -2 :
		$content = 'Friends Only';
		break;
	// Todo's
	case -10:
		$object = get_entity($item->object_guid);
		$content = "Todo: {$object->title}";
		break;
	default:
		$acl = get_access_collection($item->access_id);
		$content = $acl->name;
		break;
}
?>
<div class='river-access-display'>
	<?php echo $content; ?>
</div>
<div class='clearfix'></div>