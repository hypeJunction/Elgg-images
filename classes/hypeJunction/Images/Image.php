<?php

namespace hypeJunction\Images;

use ElggFile;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface as ImagineImageInterface;

class Image extends ElggFile implements ImageInterface {

	/**
	 * {@inheritdoc}
	 */
	public function save() {
		$return = parent::save();
		if ($return) {
			if ($this->icon_owner_guid && $this->icon_owner_guid != $this->owner_guid) {
				$this->clearThumbs();
			}
			$mtime = filemtime($this->getFilenameOnFilestore());
			if (!$this->icontime || $this->icontime != $mtime) {
				if ($this->createThumbs((int) $this->x1, (int) $this->y1, (int) $this->x2, (int) $this->y2)) {
					$this->icontime = $mtime;
				} else {
					return false;
				}
			}
		}
		return $return;
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete() {
		$this->clearThumbs();
		return parent::delete();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getThumb($size = 'medium') {

		if (!$this->guid) {
			return false;
		}

		if (!array_key_exists($size, $this->getThumbSizes())) {
			return false;
		}

		$directory = $this->getThumbDirectory();
		$filename = $this->getThumbFilename($size);

		$thumb = new Thumb();
		$thumb->owner_guid = $this->icon_owner_guid ? : $this->owner_guid;
		$thumb->setFilename("$directory/$filename");
		if (!$thumb->exists()) {
			return false;
		}

		return $thumb;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getThumbSizes() {
		$defaults = (array) elgg_get_config('icon_sizes');
		$params = [
			'entity' => $this,
		];
		return elgg_trigger_plugin_hook('thumb:sizes', 'object', $params, $defaults);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getThumbDirectory() {
		$default = 'icons';
		$params = [
			'entity' => $this,
		];
		$directory = elgg_trigger_plugin_hook('thumb:directory', 'object', $params, $default);
		return trim($directory, '/');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getThumbFilename($size) {

		$mimetype = $this->detectMimeType(null, $this->mimetype);
		switch ($mimetype) {
			default :
				$ext = 'jpg';
				break;
			case 'image/png' :
				$ext = 'png';
				break;
			case 'image/gif' :
				$ext = 'gif';
				break;
		}

		$default = "{$this->guid}/{$size}.{$ext}";
		$params = [
			'entity' => $this,
			'size' => $size,
			'extension' => $ext,
		];

		return elgg_trigger_plugin_hook('thumb:filename', 'object', $params, $default);
	}

	/**
	 * {@inheritdoc}
	 */
	public function createThumbs($x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0) {

		if (!$this->guid) {
			return false;
		}

		$crop_width = $x2 - $x1;
		$crop_height = $y2 - $y1;

		$error = false;
		$thumbs = [];

		$sizes = $this->getThumbSizes();
		foreach ($sizes as $size => $opts) {

			$width = elgg_extract('w', $opts);
			$height = elgg_extract('h', $opts);
			$square = elgg_extract('square', $opts);
			$croppable = elgg_extract('croppable', $opts, $square);
			$mode = elgg_extract('mode', $opts);

			$directory = $this->getThumbDirectory();
			$filename = $this->getThumbFilename($size);

			$thumb = new Thumb();
			$thumb->owner_guid = $this->owner_guid;
			$thumb->setFilename("$directory/$filename");
			if (!$thumb->exists()) {
				$thumb->open('write');
				$thumb->close();
			}

			$thumbs[] = $thumb;

			$params = [
				'entity' => $this,
				'thumb' => $thumb,
			];
			$options = elgg_trigger_plugin_hook('thumb:options', 'object', $params, []);
			try {

				ini_set('memory_limit', '256M');

				if ($mode != 'outbound' && $mode != 'inset') {
					$mode = ($square) ? ImagineImageInterface::THUMBNAIL_OUTBOUND : ImagineImageInterface::THUMBNAIL_INSET;
				}

				$box = new Box($width, $height);
				$image = (new Imagine())->open($this->getFilenameOnFilestore());
				if ($croppable && $crop_width > 0 && $crop_height > 0) {
					$image = $image->crop(new \Imagine\Image\Point($x1, $y1), new Box($crop_width, $crop_height));
				}
				$image = $image->thumbnail($box, $mode);
				$image->save($thumb->getFilenameOnFilestore(), $options);
				unset($image);
			} catch (\Exception $ex) {
				elgg_log($ex->getMessage(), 'ERROR');
				$error = true;
			}
		}

		if ($error) {
			foreach ($thumbs as $thumb) {
				$thumb->delete();
			}
			return false;
		}

		$this->icon_owner_guid = $this->owner_guid;
		return $thumbs;
	}

	/**
	 * {@inheritdoc}
	 */
	public function clearThumbs() {
		$sizes = $this->getThumbSizes();
		foreach ($sizes as $size => $opts) {
			$thumb = $this->getThumb($size);
			if ($thumb) {
				$thumb->delete();
			}
		}
		unset($this->icontime);
		unset($this->icon_owner_guid);
		touch($this->getFilenameOnFilestore());
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDownloadUrl() {
		return elgg_get_download_url($this, true);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getInlineUrl() {
		return elgg_get_inline_url($this, true);
	}

}
