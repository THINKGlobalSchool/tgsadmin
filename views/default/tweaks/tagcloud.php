<?php
/**
 * Elgg TGSAdmin sidebar tagcloud
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2009-2010
 * @link http://www.thinkglobalschool.com
 */

// Only display on main activity page
if (elgg_is_logged_in() && elgg_get_context() == 'activity') {	
	$tags = elgg_get_tags(array('threshold' => 2, 'limit' => 150));
	
	// Shuffle tags
	shuffle($tags);
	
	// Tag Module
	echo elgg_view_module('aside', elgg_echo('tagcloud'), elgg_view("output/tagcloud", array('value' => $tags)));
}
