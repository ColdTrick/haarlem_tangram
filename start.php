<?php
/**
 * Haarlem tangram plugin
 */

// load required lib files
require_once(dirname(__FILE__) . '/lib/functions.php');
require_once(dirname(__FILE__) . '/lib/events.php');
require_once(dirname(__FILE__) . '/lib/hooks.php');
require_once(dirname(__FILE__) . '/lib/page_handlers.php');

// register default Elgg event
elgg_register_event_handler('init', 'system', 'haarlem_tangram_init');

/**
 * Called during system init
 *
 * @return void
 */
function haarlem_tangram_init() {
	
	// page handler
	elgg_register_page_handler('vacaturebank', 'haarlem_tangram_page_handler');
	
	// plugin hooks
	elgg_register_plugin_hook_handler('cron', 'hourly', 'haarlem_tangram_hourly_cron_handler');
	elgg_register_plugin_hook_handler('action', 'admin/site/flush_cache', 'haarlem_tangram_flush_cache_handler');
	
	elgg_register_plugin_hook_handler('register', 'menu:site', 'haarlem_tangram_register_site_menu', 400); // prio needed for menu_builder
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'haarlem_tangram_register_owner_block_menu');
	
	elgg_register_plugin_hook_handler('widget_url', 'widget_manager', 'haarlem_tangram_widget_url_handler');
	
	// events
	elgg_register_event_handler('upgrade', 'system', 'haarlem_tangram_upgrade');
	
	// widget
	elgg_register_widget_type('vacancies', elgg_echo('haarlem_tangram:widget:vacancies:title'), elgg_echo('haarlem_tangram:widget:vacancies:description'), 'index,groups');
	
}
