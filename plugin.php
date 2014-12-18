<?php

require_once 'conf/common.inc.php';
require_once 'inc/html.inc.php';
require_once 'inc/collectd.inc.php';

$plugin = validate_get(GET('p'), 'plugin');

html_start();

$h = array();

function show_host($host)
{
	global $plugin;
	$plugins = collectd_plugins($host);
	if(!in_array($plugin, $plugins))
		continue;
	printf("<fieldset id=\"%s\">", htmlentities($host));
	printf("<legend>%s</legend>", htmlentities($host));
	echo '<div class="graphs">';
	plugin_header($host, $plugin);
	graphs_from_plugin($host, $plugin, false);
	echo '</div>';
	printf("</fieldset>");
}

# show all categorized hosts
if (isset($CONFIG['cat']) && is_array($CONFIG['cat'])) {
	foreach($CONFIG['cat'] as $cat => $hosts) {
		$h = array_merge($h, $hosts);
		foreach($hosts as $host) {
			show_host($host);
		}
	}
}

# search for uncategorized hosts
if(!$chosts = collectd_hosts())
	printf('<p class="warn">Error: No Collectd hosts found in <em>%s</em></p>', $CONFIG['datadir']);
$uhosts = array_diff($chosts, $h);

# show all uncategorized hosts
if ($uhosts) {
	foreach($hosts as $host) {
		show_host($host);
	}
}

html_end();
