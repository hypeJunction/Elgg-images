<?php

namespace hypeJunction\Images;

use Elgg\IntegrationTestCase;

class ImageServiceThumbSizesTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'images';
	}

	public function up(): void {
		$user = $this->createUser();
		_elgg_services()->session_manager->setLoggedInUser($user);
	}

	public function down(): void {}

	public function testGetThumbSizesFallsBackToIconSizesConfig(): void {
		$user = elgg_get_logged_in_user_entity();
		$file = new \ElggFile();
		$file->owner_guid = $user->guid;
		$file->container_guid = $user->guid;
		$file->access_id = ACCESS_PUBLIC;
		$file->setSubtype('image_test');
		$file->mimetype = 'image/jpeg';
		$file->save();

		$sizes = images()->getThumbSizes($file);
		$expected = (array) elgg_get_config('icon_sizes');
		$this->assertEquals($expected, $sizes);

		$file->delete();
	}

	public function testGetThumbSizesHonoursEventOverride(): void {
		$custom = [
			'tiny' => ['w' => 16, 'h' => 16, 'square' => true],
		];

		$handler = function (\Elgg\Event $event) use ($custom) {
			return $custom;
		};
		elgg_register_event_handler('thumb:sizes', 'object', $handler);
		try {
			$user = elgg_get_logged_in_user_entity();
			$file = new \ElggFile();
			$file->owner_guid = $user->guid;
			$file->container_guid = $user->guid;
			$file->access_id = ACCESS_PUBLIC;
			$file->setSubtype('image_test');
			$file->mimetype = 'image/jpeg';
			$file->save();

			$this->assertEquals($custom, images()->getThumbSizes($file));

			$file->delete();
		} finally {
			elgg_unregister_event_handler('thumb:sizes', 'object', $handler);
		}
	}
}
