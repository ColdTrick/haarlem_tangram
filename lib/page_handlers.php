<?php
/**
 * All page handler functions ar bundled here
 */

/**
 * The page handler vor vacaturebank
 *
 * @param array $page url segments
 *
 * @return bool
 */
function haarlem_tangram_page_handler($page) {
	
	$include_file = false;
	$pages_root = dirname(dirname(__FILE__)) . '/pages/';
	
	$page_owner_guid = haarlem_tangram_get_page_owner_guid();
	if (!empty($page_owner_guid)) {
		elgg_set_page_owner_guid($page_owner_guid);
	}
	
	switch ($page[0]) {
		case 'all':
			$include_file = "{$pages_root}all.php";
			break;
		case 'view':
			set_input('vacaturenummer', $page[1]);
			
			$include_file = "{$pages_root}view.php";
			break;
		default:
			forward('vacaturebank/all');
			break;
	}
	
	if (!empty($include_file)) {
		include($include_file);
		return true;
	}
	
	return false;
}