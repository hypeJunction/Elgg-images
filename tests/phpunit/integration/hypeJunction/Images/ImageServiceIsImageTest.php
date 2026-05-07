<?php

namespace hypeJunction\Images;

use Elgg\IntegrationTestCase;

class ImageServiceIsImageTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'images';
	}

	public function up(): void {}

	public function down(): void {}

	public function testIsImageRejectsNull(): void {
		$this->assertFalse(images()->isImage(null));
	}

	public function testIsImageRejectsNonElggFile(): void {
		$user = $this->createUser();
		$this->assertFalse(images()->isImage($user));
	}

	public function testIsImageAcceptsJpegMimetype(): void {
		$file = new \ElggFile();
		$file->mimetype = 'image/jpeg';
		$this->assertTrue(images()->isImage($file));
	}

	public function testIsImageAcceptsGifMimetype(): void {
		$file = new \ElggFile();
		$file->mimetype = 'image/gif';
		$this->assertTrue(images()->isImage($file));
	}

	public function testIsImageAcceptsPngMimetype(): void {
		$file = new \ElggFile();
		$file->mimetype = 'image/png';
		$this->assertTrue(images()->isImage($file));
	}

	public function testIsImageRejectsUnsupportedMimetype(): void {
		$file = new \ElggFile();
		$file->mimetype = 'image/svg+xml';
		$this->assertFalse(images()->isImage($file));
	}

	public function testIsImageRejectsTextMimetype(): void {
		$file = new \ElggFile();
		$file->mimetype = 'text/plain';
		$this->assertFalse(images()->isImage($file));
	}

	public function testIsImageRejectsEmptyMimetypeAndNoFile(): void {
		$file = new \ElggFile();
		// no mimetype, no filestore file → falls through to false
		$this->assertFalse(images()->isImage($file));
	}
}
