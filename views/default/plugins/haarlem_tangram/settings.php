<?php

$plugin = elgg_extract('entity', $vars);

// set tangram xml url
$tangram_url = '<div>';
$tangram_url .= elgg_echo('haarlem_tangram:settings:url');
$tangram_url .= elgg_view('input/url', [
	'name' => 'params[tangram_url]',
	'value' => $plugin->tangram_url,
]);
if ($plugin->tangram_last_update_success) {
	$last_update = elgg_view_friendly_time($plugin->tangram_last_update_success);
	
	$tangram_url .= '<div class="elgg-subtext">' . elgg_echo('haarlem_tangram:settings:url:last_update_success', [$last_update]) . '</div>';
}
$tangram_url .= '</div>';

echo $tangram_url;

// page owner
$page_owner = '<div>';
$page_owner .= elgg_echo('haarlem_tangram:settings:page_owner');
$page_owner .= elgg_view('input/text', [
	'name' => 'params[page_owner_guid]',
	'value' => $plugin->page_owner_guid,
]);
$page_owner .= '<div class="elgg-subtext">' . elgg_echo('haarlem_tangram:settings:page_owner:description') . '</div>';
$page_owner .= '</div>';

echo $page_owner;