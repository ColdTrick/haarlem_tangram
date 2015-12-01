<?php
/**
 * The file is executed when the plugin get's activated
 */

// register correct class for future use
if (get_subtype_id('object', TangramVacancy::SUBTYPE)) {
	update_subtype('object', TangramVacancy::SUBTYPE, 'TangramVacancy');
} else {
	add_subtype('object', TangramVacancy::SUBTYPE, 'TangramVacancy');
}
