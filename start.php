<?php

/**
 * Images
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2015, Ismayil Khayredinov
 */
require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', 'images_init');

/**
 * Initialize the plugin
 * @return void
 */
function images_init() {

	// By default, do not allow images to be added anywhere
	// Plugins need to override this setting on need basis, e.g. to allow images in albums
	elgg_register_plugin_hook_handler('container_permissions_check', 'object', 'images_permissions_check');
	elgg_register_plugin_hook_handler('permissions_check:comment', 'object', 'images_can_comment');

	elgg_register_plugin_hook_handler('entity:url', 'object', 'images_entity_url');
	elgg_register_plugin_hook_handler('entity:icon:url', 'object', 'images_entity_icon_url');
	elgg_register_plugin_hook_handler('thumb:sizes', 'object', 'images_thumb_sizes');

	elgg_register_action('images/upload', __DIR__ . '/actions/images/upload.php');
	elgg_register_action('images/crop', __DIR__ . '/actions/images/crop.php');

	elgg_register_page_handler('images', 'images_page_handler');

	elgg_extend_view('css/elgg', 'images.css');
}

/**
 * Container permissions filter
 * By default, images are not allowed to be stored in arbitrary containers
 *
 * @param string $hook   "container_permissions_check"
 * @param string $type   "object"
 * @param bool   $return Permission
 * @param array  $params Hook params
 * @return array
 */
function images_permissions_check($hook, $type, $return, $params) {

	$subtype = elgg_extract('subtype', $params);
	if ($subtype == 'image') {
		return false;
	}
}

/**
 * Comment permissions check
 * By default, users can not comment on images
 *
 * @param string $hook   "permissions_check:comment"
 * @param string $type   "object"
 * @param bool   $return Permission
 * @param array  $params Hook params
 * @return array
 */
function images_can_comment($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);
	if ($entity instanceof \hypeJunction\Images\Image) {
		return false;
	}
}

/**
 * Image URL handler
 *
 * @param string $hook   "entity:url"
 * @param string $type   "object"
 * @param string $return URL
 * @param array  $params Hook params
 * @return array
 */
function images_entity_url($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);
	if (!$entity instanceof hypeJunction\Images\Image) {
		return;
	}

	return elgg_normalize_url("/images/view/$entity->guid");
}

/**
 * Image icon URL handler
 *
 * @param string $hook   "entity:icon:url"
 * @param string $type   "object"
 * @param string $return URL
 * @param array  $params Hook params
 * @return array
 */
function images_entity_icon_url($hook, $type, $return, $params) {

	$size = elgg_extract('size', $params, 'medium');
	$entity = elgg_extract('entity', $params);
	if (!$entity instanceof hypeJunction\Images\Image) {
		return;
	}

	$thumb = $entity->getThumb($size);
	if (!$thumb) {
		return;
	}

	return elgg_get_inline_url($thumb, true);
}

/**
 * Modify thumb sizes config
 *
 * @param string $hook   "thumb:sizes"
 * @param string $type   "object"
 * @param array  $return Thumb sizes
 * @param array  $params Hook params
 * @return array
 */
function images_thumb_sizes($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);
	if (!$entity instanceof hypeJunction\Images\Image) {
		return;
	}

	// Make large thumbs square
	$return['large']['square'] = true;

	// Add an extra large non-square preview
	$return['extra_large'] = [
		'w' => 325,
		'h' => 325,
		'square' => false,
	];

	// Suitable for use with card item view
	$return['media'] = [
		'w' => 640,
		'h' => 360,
		'square' => false,
		'croppable' => true,
		'mode' => 'outbound',
	];

	return $return;
}

/**
 * Page handler
 *
 * @param array  $segments   URL segments
 * @param string $identifier Page Identifier
 * @return bool
 */
function images_page_handler($segments, $identifier) {

	$page = array_shift($segments);

	switch ($page) {
		case 'all' :
			echo elgg_view("resources/images/all", [
				'container_guid' => $segments[0],
				'identifier' => $identifier,
			]);
			return true;

		case 'upload' :
			echo elgg_view("resources/images/upload", [
				'container_guid' => $segments[0],
				'identifier' => $identifier,
			]);
			return true;

		case 'edit' :
			echo elgg_view("resources/images/edit", [
				'guid' => $segments[0],
				'identifier' => $identifier,
			]);
			return true;

		case 'view' :
			echo elgg_view("resources/images/view", [
				'guid' => $segments[0],
				'identifier' => $identifier,
			]);
			return true;
	}

	return false;
}
