<?php
/**
 * Set up and configure Spot plugins
 * 
 * Sets up Elgg DB and creates settings files (settings.php and .htaccess)
 */

if (PHP_SAPI !== 'cli') {
	echo "You must use the command line to run this script.";
	exit;
}

// Suppress notices
ini_set('error_reporting',E_ALL ^ E_NOTICE);

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");

// Get all plugins
$plugins = elgg_get_plugins('inactive');

foreach($plugins as $plugin) {
	if ($plugin->getManifest()->getActivateOnInstall()) {	
		foreach ($plugin->getManifest()->getRequires() as $require) {
			if ($require['type'] == 'priority') {
				$req_plugin = elgg_get_plugin_from_id($require['plugin']);
				if ($req_plugin instanceof ElggPlugin) {
					if ($require['priority'] == 'before') {
						$plugin->setPriority($req_plugin->getPriority() - 1);
					} else if ($require['priority'] == 'after') {
						$plugin->setPriority($req_plugin->getPriority() + 1);
					}
				}				
			}
		}
		$activate_plugins[$plugin->getPriority()] = $plugin;
	}
}

foreach ($activate_plugins as $key => $plugin) {
	if (!$plugin->isActive()) {
		$plugin->activate();
	}
}

elgg_invalidate_simplecache();
elgg_reset_system_cache();