<?php
/**
 * All plugin hook handlers are bundled in this file
 */

/**
 * Listen to the cron handlers
 *
 * @param string $hook         the name of the hook
 * @param string $type         the type of the hook
 * @param mixed  $return_value current return value
 * @param array  $params       supplied params
 *
 * @return void
 */
function haarlem_tangram_hourly_cron_handler($hook, $type, $return_value, $params) {
	
	// cache the tangram XML
	$cached = haarlem_tangram_cache_xml();
	if (!$cached) {
		return;
	}
	
	// store vacaturenummers to find if new vacancies were added
	$vacaturenummers = haarlem_tangram_store_vacaturenummers();
	if (empty($vacaturenummers)) {
		return;
	}
	
	haarlem_tangram_notify_group_members($vacaturenummers);
}

/**
 * Listen to the action admin/site/flush_cache
 *
 * @param string $hook         the name of the hook
 * @param string $type         the type of the hook
 * @param mixed  $return_value current return value
 * @param array  $params       supplied params
 *
 * @return void
 */
function haarlem_tangram_flush_cache_handler($hook, $type, $return_value, $params) {
	
	// reset xml cache
	haarlem_tangram_clear_cached_xml();
}

/**
 * Add menu item to site menu
 *
 * @param string         $hook         the name of the hook
 * @param string         $type         the type of the hook
 * @param ElggMenuItem[] $return_value current return value
 * @param array          $params       supplied params
 *
 * @return void|ElggMenuItem[]
 */
function haarlem_tangram_register_site_menu($hook, $type, $return_value, $params) {
	
	if (!is_array($return_value)) {
		return;
	}
	
	if (haarlem_tangram_get_page_owner_guid()) {
		// a page owner was configured, so don't add site menu
		return;
	}
	
	$return_value[] = ElggMenuItem::factory(array(
		'name' => 'haarlem_tangram',
		'text' => elgg_echo('haarlem_tangram:site_menu:title'),
		'href' => 'vacaturebank/all',
	));
	
	return $return_value;
}

/**
 * Add menu item to owner_block
 *
 * @param string         $hook         the name of the hook
 * @param string         $type         the type of the hook
 * @param ElggMenuItem[] $return_value current return value
 * @param array          $params       supplied params
 *
 * @return void|ElggMenuItem[]
 */
function haarlem_tangram_register_owner_block_menu($hook, $type, $return_value, $params) {
	
	if (!is_array($return_value)) {
		return;
	}
	
	if (empty($params) || !is_array($params)) {
		return;
	}
	
	$page_owner_guid = haarlem_tangram_get_page_owner_guid();
	if (empty($page_owner_guid)) {
		// no page owner was configured, so don't add owner_block menu
		return;
	}
	
	$entity = elgg_extract('entity', $params);
	if (!($entity instanceof ElggEntity)) {
		return;
	}
	
	if ($entity->getGUID() !== $page_owner_guid) {
		// not this entity
		return;
	}
	
	$return_value[] = ElggMenuItem::factory(array(
		'name' => 'haarlem_tangram',
		'text' => elgg_echo('haarlem_tangram:site_menu:title'),
		'href' => 'vacaturebank/all',
	));
	
	return $return_value;
}

/**
 * Set the correct URL for a widget title
 *
 * @param string         $hook         the name of the hook
 * @param string         $type         the type of the hook
 * @param ElggMenuItem[] $return_value current return value
 * @param array          $params       supplied params
 *
 * @return void|string
 */
function haarlem_tangram_widget_url_handler($hook, $type, $return_value, $params) {
	
	if (!empty($return_value)) {
		// url already set
		return;
	}
	
	if (empty($params) || !is_array($params)) {
		return;
	}
	
	$entity = elgg_extract('entity', $params);
	if (!($entity instanceof ElggWidget)) {
		return;
	}
	
	switch ($entity->handler) {
		case 'vacancies':
			$return_value = 'vacaturebank/all';
			break;
	}
	
	return $return_value;
}
