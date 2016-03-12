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

	elgg_register_plugin_hook_handler('entity:icon:url', 'all', 'images_entity_icon_url');

	elgg_register_event_handler('create', 'object', 'images_update_event_handler');
	elgg_register_event_handler('update:after', 'object', 'images_update_event_handler');

	elgg_register_event_handler('update:after', 'all', 'images_update_avatar_access');

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

	if (!$entity instanceof ElggEntity || !$entity->icontime) {
		return;
	}

	if ($entity->getSubtype() == 'file' && !elgg_get_plugin_setting('override_file_thumb_dimensions', 'images')) {
		return;
	}

	if (elgg_images_is_image($entity)) {
		$thumb = elgg_images_get_thumb($entity, $size);
	} else {
		$avatar = elgg_images_get_avatar($entity);
		if ($avatar) {
			$thumb = elgg_images_get_thumb($avatar, $size);
		}
	}

	if (!$thumb instanceof ElggFile) {
		return;
	}

	$url = elgg_get_inline_url($thumb, true);
	if (!$url) {
		return;
	}

	return $url;
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

	if (!elgg_images_is_image($entity)) {
		return;
	}

	if ($entity->getSubtype() == 'file' && !elgg_get_plugin_setting('override_file_thumb_dimensions', 'images')) {
		return;
	}

	if ($entity->icon_owner_guid && $entity->icon_owner_guid != $entity->owner_guid) {
		// Owner has changed
		elgg_images_clear_thumbs($entity);
	}

	$mtime = filemtime($entity->getFilenameOnFilestore());
	if (!$entity->icontime || $entity->icontime != $mtime) {
		if (elgg_images_create_thumbs($entity)) {
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
	return elgg_images_clear_thumbs($entity);
}

/**
 * Update avatar access id when entity is saved
 *
 * @param string     $event  "update:after"
 * @param string     $type   "all"
 * @param ElggEntity $entity Entity
 */
function images_update_avatar_access($event, $type, $entity) {

	if (!$entity instanceof ElggEntity) {
		return;
	}

	$access_id = (int) $entity->access_id;
	$avatars = images()->getAvatars($entity);

	if (!$avatars) {
		return;
	}

	foreach ($avatars as $avatar) {
		$avatar->access_id = $access_id;
		$avatar->save();
	}
}
