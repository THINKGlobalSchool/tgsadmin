<?php
/**
 * Elgg TGSAdmin externallinks JS
 *
 * @package ElggTGSAdmin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2009-2010
 * @link http://www.thinkglobalschool.com
 *
 */
?>

elgg.provide('elgg.externallinks');

// Check agains pre-defined exceptions (facebook for now)
elgg.externallinks.checkExceptions = function(str) {
	var exceptions = new Array(
		'.*?(www\\.facebook\\.com)(\\/)(login\\.php)' // Facebook login exception
	);
	
	for (exception in exceptions) {
		var p = new RegExp(exception, ["i"]);
		if (p.exec(str)) {
			return true; // Return true if we match
		}
	}
}

// Check if string starts with str
String.prototype.startsWith = function(str){
    return (this.indexOf(str) === 0);
}

// Trim HTTP or HTTPS from a url string
elgg.externallinks.trimProtocol = function(str) {
	if (str) {
		if (str.startsWith("http://"))
			return str.substr(7);
		else if (str.startsWith("https://"))
			return str.substr(8);
		else 
			return str;
	}
	return false;
}

elgg.externallinks.init = function() {
	$(document).ready(function() {	
		$("a").click(
			function () {
				var url = elgg.externallinks.trimProtocol("<?php global $CONFIG; echo $CONFIG->wwwroot; ?>");
				var href = elgg.externallinks.trimProtocol($(this).attr('href'));

				if (href && $(this).attr('href').startsWith("http") && !elgg.externallinks.checkExceptions(href)) {					
					if (!href.startsWith(url)) {
						window.open($(this).attr('href'));
						return false;
					} 
				}
			}
		);
	});	
}

elgg.register_hook_handler('init', 'system', elgg.externallinks.init);