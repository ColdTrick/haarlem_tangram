<?php

$widget = elgg_extract('entity', $vars);

$vacancy_category_options = [
	'' => elgg_echo('all'),
	'internal' => elgg_echo('haarlem_tangram:interal:title'),
	'external' => elgg_echo('haarlem_tangram:external:title'),
];

echo '<div>';
echo elgg_echo('haarlem_tangram:widget:vacancy_category');
echo elgg_view('input/dropdown', [
	'name' => 'params[vacancy_category]',
	'value' => $widget->vacancy_category,
	'options_values' => $vacancy_category_options,
	'class' => 'mls',
]);
echo '</div>';
