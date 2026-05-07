<?php

namespace hypeJunction\Images;

use Elgg\Includer;
use Elgg\PluginBootstrap;

/**
 * Bootstrap class.
 */
class Bootstrap extends PluginBootstrap {

	/**
	 * {@inheritdoc}
	 */
	public function load() {
		Includer::requireFileOnce($this->plugin->getPath() . '/autoloader.php');
	}

	/**
	 * {@inheritdoc}
	 */
	public function boot() {
	}

	/**
	 * {@inheritdoc}
	 */
	public function init() {
		elgg_register_event_handler('entity:icon:url', 'object', function(\Elgg\Event $event) {
			$params = $event->getParams();
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
		});

		elgg_register_event_handler('create', 'object', function(\Elgg\Event $event) {
			$entity = $event->getObject();
			if (!images()->isImage($entity) || !$entity instanceof \ElggFile || !$entity->exists()) {
				return;
			}

			if ($entity->icon_owner_guid && $entity->icon_owner_guid != $entity->owner_guid) {
				images()->clearThumbs($entity);
			}

			$mtime = filemtime($entity->getFilenameOnFilestore());
			if (!$entity->icontime || $entity->icontime != $mtime) {
				if (images()->createThumbs($entity)) {
					$entity->icontime = $mtime;
				}
			}
		});

		elgg_register_event_handler('update:after', 'object', function(\Elgg\Event $event) {
			$entity = $event->getObject();
			if (!images()->isImage($entity) || !$entity instanceof \ElggFile || !$entity->exists()) {
				return;
			}

			if ($entity->icon_owner_guid && $entity->icon_owner_guid != $entity->owner_guid) {
				images()->clearThumbs($entity);
			}

			$mtime = filemtime($entity->getFilenameOnFilestore());
			if (!$entity->icontime || $entity->icontime != $mtime) {
				if (images()->createThumbs($entity)) {
					$entity->icontime = $mtime;
				}
			}
		});

		elgg_register_event_handler('delete', 'object', function(\Elgg\Event $event) {
			$entity = $event->getObject();
			if ($entity instanceof \ElggEntity) {
				images()->clearThumbs($entity);
			}
		}, 999);
	}

	/**
	 * {@inheritdoc}
	 */
	public function ready() {
	}

	/**
	 * {@inheritdoc}
	 */
	public function shutdown() {
	}

	/**
	 * {@inheritdoc}
	 */
	public function activate() {
	}

	/**
	 * {@inheritdoc}
	 */
	public function deactivate() {
	}

	/**
	 * {@inheritdoc}
	 */
	public function upgrade() {
	}
}
