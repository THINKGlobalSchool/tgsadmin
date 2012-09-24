<?php
/**
 * Elgg TGSAdmin externallinks JS
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
elgg.provide('elgg.externallinks');

// Check agains pre-defined exceptions (facebook for now)
elgg.externallinks.checkExceptions = function(str) {
	var exceptions = new Array(
		'.*?(www\\.facebook\\.com)(\\/)(dialog)' // Facebook login exception
	);
	
	for (exception in exceptions) {
		var p = new RegExp(exceptions[exception], ["i"]);
		if (p.exec(str)) {
			return true; // Return true if we match
		}
	}
}

elgg.externallinks.isValidExternalLink = function(a) {
	var url = elgg.externallinks.trimProtocol("<?php global $CONFIG; echo $CONFIG->wwwroot; ?>");
	var href = elgg.externallinks.trimProtocol(a.attr('href'));
	
	if (href 
		&& a.attr('href').startsWith("http") 
		&& !elgg.externallinks.checkExceptions(href)
		&& !href.startsWith(url)
		&& !a.hasClass('elgg-toggler') 	// Check for elgg-toggler class
		&& !a.hasClass('elgg-lightbox')	// Check for elgg-lightbox class
		&& !a.hasClass('simplekaltura-lightbox')	// Check for simplekaltura-lightbox class
		&& a.attr('rel') != 'popup')  	// Check for rel=popup
	{					
		return true; 
	}

	return false;
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
		$("a").click(function (event) {
			if (elgg.externallinks.isValidExternalLink($(this))) {
				window.open($(this).attr('href'));
				return false;
			}
			//event.preventDefault();
		});
}

elgg.register_hook_handler('init', 'system', elgg.externallinks.init);