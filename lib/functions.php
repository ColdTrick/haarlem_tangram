<?php
/**
 * All helper functions are bundled here
 */

/**
 * Cache the tangram vacancy xml
 *
 * @return bool
 */
function haarlem_tangram_cache_xml() {
	
	$last_try = (int) haarlem_tangram_get_setting('tangram_last_update');
	$tangram_url = haarlem_tangram_get_setting('tangram_url');
	
	if (empty($tangram_url)) {
		return false;
	}
	
	if ($last_try > (time() - (60 * 60))) {
		// prevent deadloop tries
		return false;
	}
	
	// store last try to prevent deadloops
	elgg_set_plugin_setting('tangram_last_update', time(), 'haarlem_tangram');
	
	// prepare cURL call
	$ch = curl_init($tangram_url);
	
	// settings
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	// do the request
	$content = curl_exec($ch);
	$curl_info = curl_getinfo($ch);
	
	// close curl
	curl_close($ch);
	
	// verify output
	if (elgg_extract('http_code', $curl_info) !== 200 || stristr(elgg_extract('content_type', $curl_info), 'text/xml') === false) {
		// something went wrong
		return false;
	}
	
	// save output
	$plugin = elgg_get_plugin_from_id('haarlem_tangram');
	$fh = new ElggFile();
	$fh->owner_guid = $plugin->getGUID();
	$fh->setFilename('tangram.xml');
	
	$fh->open('write');
	$fh->write($content);
	$fh->close();
	
	// set last success update
	elgg_set_plugin_setting('tangram_last_update_success', time(), 'haarlem_tangram');
	
	return true;
}

/**
 * Get a plugin setting
 *
 * @param string $setting the name of the setting
 *
 * @return false|mixed
 */
function haarlem_tangram_get_setting($setting) {
	static $settings_cache;
	
	if (empty($setting)) {
		return false;
	}
	
	if (!isset($settings_cache)) {
		$settings_cache = [
			// optional defaults
		];
		
		$plugin = elgg_get_plugin_from_id('haarlem_tangram');
		$settings = $plugin->getAllSettings();
		if (!empty($settings)) {
			foreach ($settings as $key => $value) {
				$settings_cache[$key] = $value;
			}
		}
	}
	
	return elgg_extract($setting, $settings_cache, false);
}

/**
 * Load the xml to be used in furter processes
 *
 * @return false|SimpleXMLElement
 */
function haarlem_tangram_get_xml() {
	static $xml_cache;
	
	if (isset($xml_cache)) {
		return $xml_cache;
	}
	
	// default to failure
	$xml_cache = false;
	
	// get the file
	$plugin = elgg_get_plugin_from_id('haarlem_tangram');
	$fh = new ElggFile();
	$fh->owner_guid = $plugin->getGUID();
	$fh->setFilename('tangram.xml');
	
	if (!$fh->exists()) {
		// try a reload once
		haarlem_tangram_cache_xml();
	}
	
	if (!$fh->exists()) {
		return false;
	}
	
	$xml_cache = simplexml_load_file($fh->getFilenameOnFilestore());
	
	return $xml_cache;
}

/**
 * Remove the cached tangram xml
 *
 * @return void
 */
function haarlem_tangram_clear_cached_xml() {
	
	// unset deadloop timer
	elgg_unset_plugin_setting('tangram_last_update', 'haarlem_tangram');
	
	// remove the file from the system
	$plugin = elgg_get_plugin_from_id('haarlem_tangram');
	$fh = new ElggFile();
	$fh->owner_guid = $plugin->getGUID();
	$fh->setFilename('tangram.xml');
	
	if ($fh->exists()) {
		$fh->delete();
	}
}

/**
 * Get all interal vacancies
 *
 * @return false|TangramVacancy[]
 */
function haarlem_tangram_get_internal_vacancies() {
	$result = false;
	
	$xml = haarlem_tangram_get_xml();
	if (empty($xml)) {
		return false;
	}
	
	foreach ($xml->Vacature as $vacancy) {
		// is it interal
		if (empty($vacancy->Administratie->Datum_publ_internstart)) {
			continue;
		}
		
		$start_date = strtotime($vacancy->Administratie->Datum_publ_internstart);
		if ($start_date > time()) {
			// no yet open
			continue;
		}
		
		// check end date
		if (!empty($vacancy->Administratie->Datum_publ_internstop)) {
			$end_date = strtotime($vacancy->Administratie->Datum_publ_internstop);
			if ($end_date < time()) {
				// already ended
				continue;
			}
		}
		
		$result[] = haarlem_tangram_xml_vacancy_to_entity($vacancy);
	}
	
	return $result;
}

/**
 * Get all external vacancies
 *
 * @return false|TangramVacancy[]
 */
function haarlem_tangram_get_external_vacancies() {
	$result = false;
	
	$xml = haarlem_tangram_get_xml();
	if (empty($xml)) {
		return false;
	}
	
	foreach ($xml->Vacature as $vacancy) {
		// is it interal
		if (empty($vacancy->Administratie->Datum_publ_bpstart)) {
			continue;
		}
	
		$start_date = strtotime($vacancy->Administratie->Datum_publ_bpstart);
		if ($start_date > time()) {
			// no yet open
			continue;
		}
	
		// check end date
		if (!empty($vacancy->Administratie->Datum_publ_stop)) {
			$end_date = strtotime($vacancy->Administratie->Datum_publ_stop);
// 			if ($end_date < time()) {
// 				// already ended
// 				continue;
// 			}
		}
	
		$result[] = haarlem_tangram_xml_vacancy_to_entity($vacancy);
	}
	
	return $result;
}

/**
 * Convert the xml vacancy to an ElggEntity
 *
 * @param SimpleXMLElement $vacancy the XML vacancy
 *
 * @return false|TangramVacancy
 */
function haarlem_tangram_xml_vacancy_to_entity($vacancy) {
	
	if (empty($vacancy) || !($vacancy instanceof SimpleXMLElement)) {
		return false;
	}
	
	$entity = new TangramVacancy();
	$entity->setXmlSource($vacancy);
	
	if (!empty($vacancy->Administratie->Datum_gewijzigd)) {
		$entity->last_updated = strtotime($vacancy->Administratie->Datum_gewijzigd);
	}
	
	$entity->title = $vacancy->Vacaturetitel;
	$entity->description = $vacancy->Functie->Omschrijving;
	
	
	
	return $entity;
}

/**
 * Find a vacancy in the xml
 *
 * @param string $vacaturenummer the vacancy number
 *
 * @return false|TangramVacancy
 */
function haarlem_tangram_find_vacancy($vacaturenummer) {
	
	$xml = haarlem_tangram_get_xml();
	if (empty($xml)) {
		return false;
	}
	
	foreach ($xml->Vacature as $vacancy) {
		
		if ((string) $vacancy->Vacaturenummer !== $vacaturenummer) {
			continue;
		}
		
		return haarlem_tangram_xml_vacancy_to_entity($vacancy);
	}
	
	return false;
}

/**
 * Get the configured page_owner_guid from the plugin settings.
 *
 * The returned int is a valid user/group
 *
 * @return false|int
 */
function haarlem_tangram_get_page_owner_guid() {
	
	$page_owner_guid = (int) haarlem_tangram_get_setting('page_owner_guid');
	if ($page_owner_guid <= 0) {
		return false;
	}
	
	$page_owner = get_entity($page_owner_guid);
	if (!($page_owner instanceof ElggUser) && !($page_owner instanceof ElggGroup)) {
		return false;
	}
	
	return $page_owner_guid;
}

/**
 * Store vacaturenummers from the XML in a cache file to find out if new vacancies were added
 *
 * @return false|string[]
 */
function haarlem_tangram_store_vacaturenummers() {
	
	$xml = haarlem_tangram_get_xml();
	if (empty($xml)) {
		return false;
	}
	
	$stored = [];
	$new = [];
	
	$plugin = elgg_get_plugin_from_id('haarlem_tangram');
	$fh = new ElggFile();
	$fh->owner_guid = $plugin->getGUID();
	$fh->setFilename('vacaturenummer.cache');
	
	if ($fh->exists()) {
		$stored = unserialize($fh->grabFile());
	}
	
	// go through the xml to find all the numbers
	foreach ($xml->Vacature as $xml_vacancy) {
		
		$vacaturenummer = (string) $xml_vacancy->Vacaturenummer;
		if (in_array($vacaturenummer, $stored)) {
			continue;
		}
		
		$vacancy = haarlem_tangram_xml_vacancy_to_entity($xml_vacancy);
		if (!$vacancy->isInternal() && !$vacancy->isExternal()) {
			// not open (internaly or externaly)
			continue;
		}
		
		$new[] = $vacaturenummer;
		$stored[] = $vacaturenummer;
	}
	
	$fh->open('write');
	$fh->write(serialize($stored));
	$fh->close();
	
	return $new;
}

/**
 * Notify all group members about new vacancies
 *
 * @param string[] $vacaturenummers new vacaturenummers
 *
 * @return bool
 */
function haarlem_tangram_notify_group_members($vacaturenummers) {
	global $NOTIFICATION_HANDLERS;
	
	if (empty($vacaturenummers) || !is_array($vacaturenummers)) {
		return false;
	}
	
	$group_guid = haarlem_tangram_get_page_owner_guid();
	if (empty($group_guid)) {
		return false;
	}
	
	$entity = get_entity($group_guid);
	if (!$entity instanceof ElggGroup) {
		return false;
	}
	
	$new_part = '';
	foreach ($vacaturenummers as $nummer) {
		$vacancy = haarlem_tangram_find_vacancy($nummer);
		if (empty($vacancy)) {
			// shouldn't happen
			continue;
		}
		
		$new_part .= "- {$vacancy->title} ({$vacancy->getURL()})" . PHP_EOL;
	}
	
	$subject = elgg_echo('haarlem_tangram:notify:new_vacancy:subject');
	
	foreach ($NOTIFICATION_HANDLERS as $method => $foo) {
		$options = [
			'type' => 'user',
			'limit' => false,
			'relationship' => "notify{$method}",
			'relationship_guid' => $entity->getGUID(),
			'inverse_relationship' => true,
		];
		$batch = new ElggBatch('elgg_get_entities_from_relationship', $options);
		/* @var $user ElggUser */
		foreach ($batch as $user) {
			$body = elgg_echo('haarlem_tangram:notify:new_vacancy:body', [
				$user->name,
				$new_part,
				elgg_normalize_url('vacaturebank'),
			]);
			
			notify_user($user->guid, $entity->guid, $subject, $body, null, [$method]);
		}
	}
	
	return true;
}
