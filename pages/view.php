<?php

$vacaturenummer = get_input('vacaturenummer');
if (empty($vacaturenummer)) {
	register_error(elgg_echo('error:missing_data'));
	forward(REFERER);
}

$vacancy = haarlem_tangram_find_vacancy($vacaturenummer);
if (empty($vacancy)) {
	register_error(elgg_echo('haarlem_tangram:not_found'));
	forward(REFERER);
}

// breadcrumb
elgg_push_breadcrumb(elgg_echo('haarlem_tangram:site_menu:title'), 'vacaturebank/all');
elgg_push_breadcrumb($vacancy->title);

// build page elements
$title = $vacancy->title;

$content = elgg_view_entity($vacancy, array('full_view' => true));

// build page
$page_data = elgg_view_layout('content', array(
	'title' => elgg_echo('haarlem_tangram:view:title'),
	'content' => $content,
	'filter' => '',
));

// draw page
echo elgg_view_page($title, $page_data);
