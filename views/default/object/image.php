<?php

/**
 * File renderer.
 *
 * @package ElggFile
 */
$size = elgg_extract('size', $vars, 'medium');
$full = elgg_extract('full_view', $vars);
$entity = elgg_extract('entity', $vars);

$show_header = elgg_extract('show_header', $vars, true);
$show_media = elgg_extract('show_media', $vars, true);
$show_menu = elgg_extract('show_menu', $vars, !elgg_in_context('widgets'));

if (!$entity instanceof hypeJunction\Images\Image) {
	return;
}

if (elgg_in_context('gallery')) {
	$show_header = false;
	$show_menu = false;
} else if ($full) {
	echo elgg_view('profile/object/image', $vars);
	return;
}

$menu = '';
if ($show_menu) {
	$menu = elgg_view_menu('entity', ['entity' => $entity,
		'sort_by' => 'priority',
		'class' => 'elgg-menu-hz',
	]);
}

$content = elgg_view_entity_icon($entity, $size);

if ($show_header) {
	$owner = $entity->getOwnerEntity();
	$owner_link = elgg_view('output/url', [
		'href' => $owner->getURL(),
		'text' => $owner->getDisplayName(),
	]);
	$owner_icon = elgg_view_entity_icon($owner, 'small');
	$author_text = elgg_echo('byline', [$owner_link]);
	$date = elgg_view_friendly_time($entity->time_created);

	$subtitle = "$author_text $date";

	$params = [
		'entity' => $entity,
		'metadata' => $menu,
		'subtitle' => $subtitle,
		'content' => $content,
	];

	$summary = elgg_view('object/elements/summary', $params);

	echo elgg_view_image_block($owner_icon, $summary);
} else {
	echo $content . $menu;
}