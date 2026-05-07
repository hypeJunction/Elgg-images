<?php

namespace hypeJunction\Images;

use Elgg\IntegrationTestCase;

class BootstrapTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'images';
	}

	public function up(): void {}

	public function down(): void {}

	public function testPluginIsActive(): void {
		$plugin = elgg_get_plugin_from_id('images');
		$this->assertInstanceOf(\ElggPlugin::class, $plugin);
		$this->assertTrue($plugin->isActive());
	}

	public function testImagesHelperReturnsService(): void {
		$this->assertInstanceOf(ImageService::class, images());
	}

	public function testImagesHelperReturnsSingleton(): void {
		$first = images();
		$second = images();
		$this->assertSame($first, $second);
	}

	public function testIconUrlEventRegistered(): void {
		$user = $this->createUser();
		_elgg_services()->session_manager->setLoggedInUser($user);

		$image = new Image();
		$image->owner_guid = $user->guid;
		$image->container_guid = $user->guid;
		$image->access_id = ACCESS_PUBLIC;
		$image->setSubtype('image_test');
		$image->mimetype = 'image/jpeg';
		$image->simpletype = 'image';
		$image->save();

		// no thumb file exists, so the event yields to the default value (null)
		$url = elgg_trigger_event_results('entity:icon:url', 'object', [
			'entity' => $image,
			'size' => 'medium',
		], 'fallback-url');

		$this->assertEquals('fallback-url', $url);

		$image->delete();
	}
}
