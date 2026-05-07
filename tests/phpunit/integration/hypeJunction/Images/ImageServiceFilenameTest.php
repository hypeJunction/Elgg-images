<?php

namespace hypeJunction\Images;

use Elgg\IntegrationTestCase;

class ImageServiceFilenameTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'images';
	}

	public function up(): void {
		$user = $this->createUser();
		_elgg_services()->session_manager->setLoggedInUser($user);
	}

	public function down(): void {}

	private function makeFile(string $mimetype, string $filename = ''): \ElggFile {
		$user = elgg_get_logged_in_user_entity();
		$file = new \ElggFile();
		$file->owner_guid = $user->guid;
		$file->container_guid = $user->guid;
		$file->access_id = ACCESS_PUBLIC;
		$file->setSubtype('image_test');
		$file->mimetype = $mimetype;
		if ($filename) {
			$file->setFilename($filename);
		}

		$file->save();
		return $file;
	}

	public function testGetDirectoryReturnsDefault(): void {
		$file = new \ElggFile();
		$file->owner_guid = elgg_get_logged_in_user_guid();
		$this->assertEquals('file', images()->getDirectory($file));
	}

	public function testGetDirectoryHonoursEventOverride(): void {
		$handler = function (\Elgg\Event $event) {
			return 'custom_dir';
		};
		elgg_register_event_handler('directory', 'object', $handler);
		try {
			$file = new \ElggFile();
			$file->owner_guid = elgg_get_logged_in_user_guid();
			$this->assertEquals('custom_dir', images()->getDirectory($file));
		} finally {
			elgg_unregister_event_handler('directory', 'object', $handler);
		}
	}

	public function testGetDirectoryTrimsSlashes(): void {
		$handler = function (\Elgg\Event $event) {
			return '/leading/and/trailing/';
		};
		elgg_register_event_handler('directory', 'object', $handler);
		try {
			$file = new \ElggFile();
			$file->owner_guid = elgg_get_logged_in_user_guid();
			$this->assertEquals('leading/and/trailing', images()->getDirectory($file));
		} finally {
			elgg_unregister_event_handler('directory', 'object', $handler);
		}
	}

	public function testGetThumbDirectoryReturnsDefault(): void {
		$file = new \ElggFile();
		$file->owner_guid = elgg_get_logged_in_user_guid();
		$file->mimetype = 'image/jpeg';
		$this->assertEquals('icons', images()->getThumbDirectory($file));
	}

	public function testGetThumbDirectoryHonoursEventOverride(): void {
		$handler = function (\Elgg\Event $event) {
			return 'thumb_cache';
		};
		elgg_register_event_handler('thumb:directory', 'object', $handler);
		try {
			$file = new \ElggFile();
			$file->owner_guid = elgg_get_logged_in_user_guid();
			$this->assertEquals('thumb_cache', images()->getThumbDirectory($file));
		} finally {
			elgg_unregister_event_handler('thumb:directory', 'object', $handler);
		}
	}

	public function testGetThumbFilenameUsesJpgExtensionForJpeg(): void {
		$file = $this->makeFile('image/jpeg', 'test/sample.jpg');
		$filename = images()->getThumbFilename($file, 'medium');
		$this->assertEquals("{$file->guid}/medium.jpg", $filename);
		$file->delete();
	}

	public function testGetThumbFilenameUsesPngExtensionForPng(): void {
		$file = $this->makeFile('image/png', 'test/sample.png');
		$filename = images()->getThumbFilename($file, 'small');
		$this->assertEquals("{$file->guid}/small.png", $filename);
		$file->delete();
	}

	public function testGetThumbFilenameUsesGifExtensionForGif(): void {
		$file = $this->makeFile('image/gif', 'test/sample.gif');
		$filename = images()->getThumbFilename($file, 'large');
		$this->assertEquals("{$file->guid}/large.gif", $filename);
		$file->delete();
	}

	public function testGetThumbFilenameHonoursEventOverride(): void {
		// `thumb:filename`/`object` is also fired by getFilename() with no `size`
		// param — defensively read params with a default so this handler is safe
		// to leave registered if the test fails partway through.
		$handler = function (\Elgg\Event $event) {
			$params = $event->getParams();
			$entity = elgg_extract('entity', $params);
			$size = elgg_extract('size', $params, 'default');
			return "custom/{$entity->guid}-{$size}";
		};
		elgg_register_event_handler('thumb:filename', 'object', $handler);
		try {
			$file = $this->makeFile('image/jpeg', 'test/sample.jpg');
			$filename = images()->getThumbFilename($file, 'tiny');
			$this->assertEquals("custom/{$file->guid}-tiny", $filename);
			$file->delete();
		} finally {
			elgg_unregister_event_handler('thumb:filename', 'object', $handler);
		}
	}
}
