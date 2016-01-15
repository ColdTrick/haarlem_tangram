<?php

$entity = elgg_extract('entity', $vars);
if (!($entity instanceof TangramVacancy)) {
	return;
}

$full_view = (bool) elgg_extract('full_view', $vars);

if ($full_view) {
	// full view
	
	// header image
	$large_icon = $entity->getXMLPath(array(
		'Groepspecifiek',
		'LOGO2URL',
	));
	if (!empty($large_icon)) {
		echo elgg_view_entity_icon($entity, 'master');
	}
	
	// afdeling omschrijving (Organisatie -> Afdeling -> Omschrijving)
	$department_description = $entity->getXMLPath(array(
		'Organisatie',
		'Afdeling',
		'Omschrijving',
	));
	if (empty($department_description)) {
		$department_description = $entity->getXMLPath(array(
			'Organisatie',
			'Omschrijving',
		));
	}
	if (!empty($department_description)) {
		$title = elgg_echo('haarlem_tangram:department:title');
		
		$content = elgg_view('output/longtext', array(
			'value' => (string) $department_description,
			'class' => 'mtn',
		));
		
		echo elgg_view_module('info', $title, $content, array(
			'class' => 'haarlem_tangram_medium',
		));
	}
	
	// title
	echo '<h1 class="mbl">' . $entity->title . '</h1>';
	
	// function description (Functie -> Omschrijving)
	$function = $entity->getXMLPath(array(
		'Functie',
		'Omschrijving',
	));
	if (!empty($function)) {
		$title = elgg_echo('haarlem_tangram:function:title');
		
		$content = elgg_view('output/longtext', array(
			'value' => (string) $function,
			'class' => 'mtn',
		));
		
		echo elgg_view_module('info', $title, $content, array(
			'class' => 'haarlem_tangram_medium',
		));
	}
	
	// function demands (Functie -> Eisen)
	$demands = $entity->getXMLPath(array(
		'Functie',
		'Eisen',
	));
	if (!empty($demands)) {
		$title = elgg_echo('haarlem_tangram:function:demands');
		
		$content = elgg_view('output/longtext', array(
			'value' => (string) $demands,
			'class' => 'mtn',
		));
		
		echo elgg_view_module('info', $title, $content, array(
			'class' => 'haarlem_tangram_medium',
		));
	}
	
	// what do we offer (Arbeidsvoorwaarden -> Overig)
	$offer = $entity->getXMLPath(array(
		'Arbeidsvoorwaarden',
		'Overig',
	));
	if (!empty($offer)) {
		$title = elgg_echo('haarlem_tangram:function:offer');
		
		$content = elgg_view('output/longtext', array(
			'value' => (string) $offer,
			'class' => 'mtn',
		));
		
		echo elgg_view_module('info', $title, $content, array(
			'class' => 'haarlem_tangram_medium',
		));
	}
	
	// where (Solliciterenbij -> Omschrijving
	// + link to form (Solliciterenbij -> URLOnlineFormulier)
	$where = $entity->getXMLPath(array(
		'Solliciterenbij',
		'Omschrijving',
	));
	if (!empty($where)) {
		$title = elgg_echo('haarlem_tangram:function:where');
		
		$content = elgg_view('output/longtext', array(
			'value' => (string) $where,
			'class' => 'mtn',
		));
		
		$link = $entity->getXMLPath(array(
			'Solliciterenbij',
			'URLOnlineFormulier',
		));
		
		if (!empty($link)) {
			$link = (string) $link;
			
			if ($entity->isInternal()) {
				$additional_params = array(
					'location' => 2,
				);
				$link = elgg_http_add_url_query_elements($link, $additional_params);
			}
			
			$content .= elgg_view('output/url', array(
				'text' => elgg_echo('haarlem_tangram:function:online_link'),
				'href' => $link,
				'target' => '_blank',
			));
		}
		
		echo elgg_view_module('info', $title, $content, array(
			'class' => 'haarlem_tangram_medium',
		));
	}
	
} else {
	// listing view
	$icon = elgg_view_entity_icon($entity, 'small');
	
	$subtitle = array();
	if ($entity->last_updated) {
		$subtitle[] = elgg_echo('haarlem_tangram:last_updated', array(elgg_view_friendly_time($entity->last_updated)));
	}
	
	// build output
	$params = array(
		'entity' => $entity,
		'subtitle' => implode(' ', $subtitle),
		'content' => $excerpt,
	);
	$params = $params + $vars;
	$list_body = elgg_view('object/elements/summary', $params);
	
	echo elgg_view_image_block($icon, $list_body);
}
