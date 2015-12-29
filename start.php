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

	elgg_register_plugin_hook_handler('entity:icon:url', 'object', 'images_entity_icon_url');

	elgg_register_event_handler('create', 'object', 'images_update_event_handler');
	elgg_register_event_handler('update:after', 'object', 'images_update_event_handler');
	elgg_register_event_handler('delete', 'object', 'images_delete_event_handler', 999);
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
	if (!images()->isImage($entity)) {
		return;
	}

	$thumb = images()->getThumb($entity, $size);
	if (!$thumb) {
		return;
	}

	return elgg_get_inline_url($thumb, true);
}

/**
 * Post-save handler
 *
 * @param string     $event  "create"|"update:after"
 * @param string     $type   "object"
 * @param ElggEntity $entity Entity
 * @return void
 */
function images_update_event_handler($event, $type, $entity) {

	if (!images()->isImage($entity)) {
		return;
	}

	if ($entity->icon_owner_guid && $entity->icon_owner_guid != $entity->owner_guid) {
		images()->clearThumbs($entity);
	}

	$mtime = filemtime($entity->getFilenameOnFilestore());
	if (!$entity->icontime || $entity->icontime != $mtime) {
		if (images()->createThumbs($entity)) {
			$entity->icontime = $mtime;
		} else {
			return false;
		}
	}
}

/**
 * Remove image thumbnails on entity delete
 * 
 * @param string     $event  "delete"
 * @param string     $type   "object"
 * @param ElggEntity $entity Entity
 * @return void
 */
function images_delete_handler($event, $type, $entity) {
	images()->clearThumbs($entity);
}
