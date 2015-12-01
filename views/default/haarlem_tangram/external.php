<?php

$entities = elgg_extract('entities', $vars);

$title = elgg_echo('haarlem_tangram:external:title');

if (!empty($entities)) {
	$view_options = array(
		'limit' => false,
		'offset' => 0,
		'pagination' => false,
		'full_view' => false,
	);
	
	$content = elgg_view_entity_list($entities, $view_options);
} else {
	$content = elgg_echo('haarlem_tangram:no_vacancies');
}

echo elgg_view_module('info', $title, $content);
