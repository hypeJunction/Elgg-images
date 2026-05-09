<?php

namespace hypeJunction\Images;

use Elgg\IntegrationTestCase;

class ImageEntityTest extends IntegrationTestCase {

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
     * @return void
     */
    public function testImageExtendsElggFile(): void {
		$image = new Image();
		$this->assertInstanceOf(\ElggFile::class, $image);
	}

	/**
     * @return void
     */
    public function testImageImplementsImageInterface(): void {
		$image = new Image();
		$this->assertInstanceOf(ImageInterface::class, $image);
	}

	/**
     * @return void
     */
    public function testThumbExtendsElggFile(): void {
		$thumb = new Thumb();
		$this->assertInstanceOf(\ElggFile::class, $thumb);
	}

	/**
     * @return void
     */
    public function testThumbImplementsThumbInterface(): void {
		$thumb = new Thumb();
		$this->assertInstanceOf(ThumbInterface::class, $thumb);
	}

	/**
     * @return void
     */
    public function testImageGetThumbSizesDelegatesToService(): void {
		$user = elgg_get_logged_in_user_entity();
		$image = new Image();
		$image->owner_guid = $user->guid;
		$image->container_guid = $user->guid;
		$image->access_id = ACCESS_PUBLIC;
		$image->setSubtype('image_test');
		$image->mimetype = 'image/jpeg';
		$image->save();

		$this->assertEquals(images()->getThumbSizes($image), $image->getThumbSizes());

		$image->delete();
	}

	/**
     * @return void
     */
    public function testImageGetThumbReturnsFalseWhenNoThumbExists(): void {
		$user = elgg_get_logged_in_user_entity();
		$image = new Image();
		$image->owner_guid = $user->guid;
		$image->container_guid = $user->guid;
		$image->access_id = ACCESS_PUBLIC;
		$image->setSubtype('image_test');
		$image->mimetype = 'image/jpeg';
		$image->save();

		$this->assertFalse($image->getThumb('medium'));

		$image->delete();
	}
}
