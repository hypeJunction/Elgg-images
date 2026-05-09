<?php

namespace hypeJunction\Images;

use Elgg\IntegrationTestCase;

class ImageServiceIsImageTest extends IntegrationTestCase {

	/**
     * @return string
     */
    public function getPluginID(): string {
		return 'images';
	}

	/**
     * @return void
     */
    public function up(): void {}

	/**
     * @return void
     */
    public function down(): void {}

	/**
     * @return void
     */
    public function testIsImageRejectsNull(): void {
		$this->assertFalse(images()->isImage(null));
	}

	/**
     * @return void
     */
    public function testIsImageRejectsNonElggFile(): void {
		$user = $this->createUser();
		$this->assertFalse(images()->isImage($user));
	}

	/**
     * @return void
     */
    public function testIsImageAcceptsJpegMimetype(): void {
		$file = new \ElggFile();
		$file->mimetype = 'image/jpeg';
		$this->assertTrue(images()->isImage($file));
	}

	/**
     * @return void
     */
    public function testIsImageAcceptsGifMimetype(): void {
		$file = new \ElggFile();
		$file->mimetype = 'image/gif';
		$this->assertTrue(images()->isImage($file));
	}

	/**
     * @return void
     */
    public function testIsImageAcceptsPngMimetype(): void {
		$file = new \ElggFile();
		$file->mimetype = 'image/png';
		$this->assertTrue(images()->isImage($file));
	}

	/**
     * @return void
     */
    public function testIsImageRejectsUnsupportedMimetype(): void {
		$file = new \ElggFile();
		$file->mimetype = 'image/svg+xml';
		$this->assertFalse(images()->isImage($file));
	}

	/**
     * @return void
     */
    public function testIsImageRejectsTextMimetype(): void {
		$file = new \ElggFile();
		$file->mimetype = 'text/plain';
		$this->assertFalse(images()->isImage($file));
	}

	/**
     * @return void
     */
    public function testIsImageRejectsEmptyMimetypeAndNoFile(): void {
		$file = new \ElggFile();
		// no mimetype, no filestore file → falls through to false
		$this->assertFalse(images()->isImage($file));
	}
}
