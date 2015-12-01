<?php

// breadcrumb
elgg_push_breadcrumb(elgg_echo('haarlem_tangram:site_menu:title'));

// build page elements
$title = elgg_echo('haarlem_tangram:all:title');

// interal vacancies
$internal = haarlem_tangram_get_internal_vacancies();
$content = elgg_view('haarlem_tangram/internal', array('entities' => $internal));

// external vacancies
$internal = haarlem_tangram_get_external_vacancies();
$content .= elgg_view('haarlem_tangram/external', array('entities' => $internal));

// build page
$page_data = elgg_view_layout('content', array(
	'title' => $title,
	'content' => $content,
	'filter' => '',
));

// draw page
echo elgg_view_page($title, $page_data);
