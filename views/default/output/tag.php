<?php
/**
 * Elgg single tag output
 * - This is an override
 *
 * @uses $vars['value']   String
 * @uses $vars['type']    The entity type, optional
 * @uses $vars['subtype'] The entity subtype, optional
 *
 */

if (!empty($vars['subtype'])) {
	$subtype = "&subtype=" . urlencode($vars['subtype']);
} else {
	$subtype = "";
}
if (!empty($vars['object'])) {
	$object = "&object=" . urlencode($vars['object']);
} else {
	$object = "";
}

if (isset($vars['value'])) {
	if (!empty($vars['type'])) {
		$type = "&type={$vars['type']}";
	} else {
		$type = "";
	}
	$url = elgg_get_site_url() . "tagdashboards/add/#". urlencode($vars['value']);
	echo elgg_view('output/url', array(
		'href' => $url,
		'text' => $vars['value'],
		'rel' => 'tag',
	));
}
