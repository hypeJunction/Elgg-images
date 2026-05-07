<?php

namespace hypeJunction\Images;

use Elgg\IntegrationTestCase;

/**
 * End-to-end lifecycle: createFromResource → createThumbs → getThumb → clearThumbs.
 *
 * Uses real image fixtures shipped under tests/fixtures/ so we exercise Imagine
 * rather than mocking it. Anything that breaks in the GD pipeline (mimetype
 * detection, thumbnail generation, file paths) shows up here.
 */
class ImageServiceLifecycleTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'images';
	}

	public function up(): void {
		$user = $this->createUser();
		_elgg_services()->session_manager->setLoggedInUser($user);
	}

	public function down(): void {}

	private function fixturePath(string $name): string {
		return dirname(__DIR__, 4) . '/fixtures/' . $name;
	}

	public function testCreateFromResourceWithJpegPersistsImageFile(): void {
		$user = elgg_get_logged_in_user_entity();

		$file = new \ElggFile();
		$file->setSubtype('file');
		$file->owner_guid = $user->guid;
		$file->container_guid = $user->guid;
		$file->access_id = ACCESS_PUBLIC;

		$result = images()->createFromResource($this->fixturePath('sample.jpg'), $file);

		$this->assertInstanceOf(\ElggFile::class, $result);
		$this->assertEquals('image', $result->simpletype);
		$this->assertStringStartsWith('image/jpeg', $result->mimetype);
		$this->assertTrue($result->exists());

		$result->delete();
	}

	public function testCreateFromResourceRejectsNonImage(): void {
		$user = elgg_get_logged_in_user_entity();

		$file = new \ElggFile();
		$file->setSubtype('file');
		$file->owner_guid = $user->guid;
		$file->container_guid = $user->guid;
		$file->access_id = ACCESS_PUBLIC;

		$result = images()->createFromResource($this->fixturePath('notimage.txt'), $file);

		$this->assertFalse($result);
	}

	public function testCreateFromResourceReturnsFalseWhenNoSessionAndNoOwner(): void {
		// Drop the logged-in user from up() so the implicit owner_guid fallback
		// in createFromResource (logged_in_user_guid()) yields 0, exercising the
		// "files need an owner to load a filestore" guard.
		_elgg_services()->session_manager->removeLoggedInUser();

		$result = images()->createFromResource($this->fixturePath('sample.jpg'));
		$this->assertFalse($result);
	}

	public function testCreateThumbsGeneratesThumbnailsForJpeg(): void {
		$user = elgg_get_logged_in_user_entity();

		$file = new \ElggFile();
		$file->setSubtype('file');
		$file->owner_guid = $user->guid;
		$file->container_guid = $user->guid;
		$file->access_id = ACCESS_PUBLIC;
		$file = images()->createFromResource($this->fixturePath('sample.jpg'), $file);
		$this->assertInstanceOf(\ElggFile::class, $file);

		$thumbs = images()->createThumbs($file);
		$this->assertIsArray($thumbs);
		$this->assertNotEmpty($thumbs);
		foreach ($thumbs as $thumb) {
			$this->assertInstanceOf(Thumb::class, $thumb);
			$this->assertTrue($thumb->exists(), 'thumb file should exist on filestore');
		}

		// icon_owner_guid is populated as a side-effect
		$this->assertEquals($user->guid, $file->icon_owner_guid);

		// getThumb now returns a Thumb for each registered size
		$sizes = images()->getThumbSizes($file);
		foreach (array_keys($sizes) as $size) {
			$retrieved = images()->getThumb($file, $size);
			$this->assertInstanceOf(Thumb::class, $retrieved, "thumb for size '{$size}' should be retrievable");
		}

		$file->delete();
	}

	public function testCreateThumbsRejectsNonImage(): void {
		$user = elgg_get_logged_in_user_entity();

		$file = new \ElggFile();
		$file->setSubtype('file');
		$file->owner_guid = $user->guid;
		$file->container_guid = $user->guid;
		$file->access_id = ACCESS_PUBLIC;
		$file->setFilename('test/notimage.txt');
		$file->open('write');
		$file->write('hello world');
		$file->close();
		$file->mimetype = 'text/plain';
		$file->simpletype = 'document';
		$file->save();

		$this->assertFalse(images()->createThumbs($file));

		$file->delete();
	}

	public function testGetThumbReturnsFalseForUnknownSize(): void {
		$user = elgg_get_logged_in_user_entity();

		$file = new \ElggFile();
		$file->setSubtype('file');
		$file->owner_guid = $user->guid;
		$file->container_guid = $user->guid;
		$file->access_id = ACCESS_PUBLIC;
		$file = images()->createFromResource($this->fixturePath('sample.jpg'), $file);

		$this->assertFalse(images()->getThumb($file, 'no_such_size'));

		$file->delete();
	}

	public function testGetThumbReturnsFalseForNonImage(): void {
		$user = elgg_get_logged_in_user_entity();
		$this->assertFalse(images()->getThumb($user, 'medium'));
	}

	public function testClearThumbsRemovesGeneratedFiles(): void {
		$user = elgg_get_logged_in_user_entity();

		$file = new \ElggFile();
		$file->setSubtype('file');
		$file->owner_guid = $user->guid;
		$file->container_guid = $user->guid;
		$file->access_id = ACCESS_PUBLIC;
		$file = images()->createFromResource($this->fixturePath('sample.jpg'), $file);
		$thumbs = images()->createThumbs($file);
		$this->assertNotEmpty($thumbs);
		$file->icontime = time();

		images()->clearThumbs($file);

		// After clearing, no thumb file should be retrievable.
		$sizes = images()->getThumbSizes($file);
		foreach (array_keys($sizes) as $size) {
			$this->assertFalse(images()->getThumb($file, $size), "thumb for '{$size}' should be cleared");
		}

		// Side-effects on the entity
		$this->assertEmpty($file->icontime);
		$this->assertEmpty($file->icon_owner_guid);

		$file->delete();
	}

	public function testCropChangesImageDimensions(): void {
		$user = elgg_get_logged_in_user_entity();

		$file = new \ElggFile();
		$file->setSubtype('file');
		$file->owner_guid = $user->guid;
		$file->container_guid = $user->guid;
		$file->access_id = ACCESS_PUBLIC;
		$file = images()->createFromResource($this->fixturePath('sample.jpg'), $file);

		$originalSize = filesize($file->getFilenameOnFilestore());
		$this->assertTrue(images()->crop($file, 0, 0, 20, 20));

		$cropped = getimagesize($file->getFilenameOnFilestore());
		$this->assertEquals(20, $cropped[0]);
		$this->assertEquals(20, $cropped[1]);

		$file->delete();
	}

	public function testCropRejectsNonImage(): void {
		$user = elgg_get_logged_in_user_entity();
		$this->assertFalse(images()->crop($user, 0, 0, 10, 10));
	}

	public function testCropRejectsZeroAreaCrop(): void {
		$user = elgg_get_logged_in_user_entity();

		$file = new \ElggFile();
		$file->setSubtype('file');
		$file->owner_guid = $user->guid;
		$file->container_guid = $user->guid;
		$file->access_id = ACCESS_PUBLIC;
		$file = images()->createFromResource($this->fixturePath('sample.jpg'), $file);

		$this->assertFalse(images()->crop($file, 0, 0, 0, 0));

		$file->delete();
	}
}
