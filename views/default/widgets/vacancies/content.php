<?php

$widget = elgg_extract('entity', $vars);

// interal vacancies
$internal = haarlem_tangram_get_internal_vacancies();
$content = elgg_view('haarlem_tangram/internal', array('entities' => $internal));

// external vacancies
$internal = haarlem_tangram_get_external_vacancies();
$content .= elgg_view('haarlem_tangram/external', array('entities' => $internal));

echo $content;
