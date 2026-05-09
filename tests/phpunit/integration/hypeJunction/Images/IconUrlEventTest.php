<?php

namespace hypeJunction\Images;

use Elgg\IntegrationTestCase;

/**
 * The plugin registers an `entity:icon:url`/`object` event handler that returns
 * the inline thumbnail URL when the entity passes `isImage()` and the requested
 * thumb exists on the filestore. These tests cover both the "yes, this is an
 * image with a thumb" path and the early-return paths that yield to other
 * handlers (no mimetype, no thumb file).
 */
class IconUrlEventTest extends IntegrationTestCase {

	/**
     * @return string
     */
    public function getPluginID(): string {
		return 'images';
	}

	/**
     * @return void
     */
    public function up(): void {
		$user = $this->createUser();
		_elgg_services()->session_manager->setLoggedInUser($user);
	}

	/**
     * @return void
     */
    public function down(): void {}

	/**
     * @param string $name
     * @return string
     */
    private function fixturePath(string $name): string {
		return dirname(__DIR__, 4) . '/fixtures/' . $name;
	}

	/**
     * @return void
     */
    public function testIconUrlReturnsThumbUrlForImage(): void {
		$user = elgg_get_logged_in_user_entity();

		$file = new \ElggFile();
		$file->setSubtype('file');
		$file->owner_guid = $user->guid;
		$file->container_guid = $user->guid;
		$file->access_id = ACCESS_PUBLIC;
		$file = images()->createFromResource($this->fixturePath('sample.jpg'), $file);

		$this->assertNotEmpty(images()->createThumbs($file));

		$url = elgg_trigger_event_results('entity:icon:url', 'object', [
			'entity' => $file,
			'size' => 'medium',
		], null);

		$this->assertIsString($url);
		$this->assertNotEmpty($url);

		$file->delete();
	}

	/**
     * @return void
     */
    public function testIconUrlYieldsForNonImageEntity(): void {
		$user = elgg_get_logged_in_user_entity();

		$default = 'fallback-url';
		$url = elgg_trigger_event_results('entity:icon:url', 'object', [
			'entity' => $user, // not an ElggFile / not image
			'size' => 'medium',
		], $default);

		$this->assertEquals($default, $url);
	}

	/**
     * @return void
     */
    public function testIconUrlYieldsWhenNoThumbExists(): void {
		$user = elgg_get_logged_in_user_entity();

		$file = new \ElggFile();
		$file->owner_guid = $user->guid;
		$file->container_guid = $user->guid;
		$file->access_id = ACCESS_PUBLIC;
		$file->setSubtype('image_test');
		$file->mimetype = 'image/jpeg';
		// no thumb generated, no real file on filestore
		$file->save();

		$default = 'fallback-url';
		$url = elgg_trigger_event_results('entity:icon:url', 'object', [
			'entity' => $file,
			'size' => 'medium',
		], $default);

		$this->assertEquals($default, $url);

		$file->delete();
	}
}
