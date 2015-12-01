<?php

$widget = elgg_extract('entity', $vars);

$content = '';

if ($widget->vacancy_category !== 'external') {
	// interal vacancies
	$internal = haarlem_tangram_get_internal_vacancies();
	$content = elgg_view('haarlem_tangram/internal', array('entities' => $internal));
}

if ($widget->vacancy_category !== 'internal') {
	// external vacancies
	$internal = haarlem_tangram_get_external_vacancies();
	$content .= elgg_view('haarlem_tangram/external', array('entities' => $internal));
}

echo $content;
