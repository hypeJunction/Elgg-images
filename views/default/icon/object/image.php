<?php

/**
 * Generic icon view.
 *
 * @package Elgg
 * @subpackage Core
 *
 * @uses $vars['entity']     The entity the icon represents - uses getIconURL() method
 * @uses $vars['size']       topbar, tiny, small, medium (default), large, master
 * @uses $vars['href']       Optional override for link
 * @uses $vars['img_class']  Optional CSS class added to img
 * @uses $vars['link_class'] Optional CSS class for the link
 * @uses $vars['circle']    Make icons circle
 */
$entity = elgg_extract('entity', $vars);

$size = elgg_extract('size', $vars, 'medium');
$icon_type = elgg_extract('icon_type', $vars, 'icon');
$width = elgg_extract('width', $vars);
$height = elgg_extract('height', $vars);

if (!$entity instanceof hypeJunction\Images\Image) {
	return;
}

$href = $entity->getURL();
if (isset($vars['href'])) {
	$href = $vars['href'];
}

$img_class = (array) elgg_extract('img_class', $vars, []);

$title = elgg_extract('title', $vars, $entity->getDisplayName());

$img = elgg_view('output/img', [
	'src' => $entity->getIconURL([
		'size' => $size,
		'type' => $icon_type
	]),
	'alt' => $title,
	'width' => $width,
	'height' => $height,
	'class' => $img_class,
		]);

if ($href && elgg_extract('use_link', $vars, true)) {
	$link_class = (array) elgg_extract('link_class', $vars, []);
	$link_class[] = 'elgg-tooltip';
	$img = elgg_view('output/url', [
		'is_trusted' => true,
		'class' => $link_class,
		'text' => $img,
		'href' => $href,
		'title' => $title,
	]);
}

$wrapper_class = (array) elgg_extract('class', $vars, []);
$wrapper_class[] = "images-thumbnail";
$wrapper_class[] = "images-thumbnail-$size";

echo elgg_format_element('div', [
	'class' => $wrapper_class,
		], $img);
