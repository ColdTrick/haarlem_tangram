<?php
/**
 * All event handlers are bundled in this file
 */

/**
 * Listen to the upgrade event
 *
 * @param string $event  the name of the event
 * @param string $type   the type of the event
 * @param mixed  $object supplied params
 */
function haarlem_tangram_upgrade($event, $type, $object) {
	
	// register correct class for future use
	if (get_subtype_id('object', TangramVacancy::SUBTYPE)) {
		update_subtype('object', TangramVacancy::SUBTYPE, 'TangramVacancy');
	} else {
		add_subtype('object', TangramVacancy::SUBTYPE, 'TangramVacancy');
	}
	
	// reset xml cache
	haarlem_tangram_clear_cached_xml();
}
