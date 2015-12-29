<?php

namespace hypeJunction\Images;

use ElggFile;

class Image extends ElggFile implements ImageInterface {

	/**
	 * {@inheritdoc}
	 */
	public function getThumb($size = 'medium') {
		return images()->getThumb($size);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getThumbSizes() {
		return images()->getThumbSizes($this);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getThumbDirectory() {
		return images()->getThumbDirectory($this);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getThumbFilename($size) {
		return images()->getThumbFilename($this, $size);
	}

	/**
	 * {@inheritdoc}
	 */
	public function createThumbs($x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0) {
		return images()->createThumbs($x1, $y1, $x2, $y2);
	}

	/**
	 * {@inheritdoc}
	 */
	public function clearThumbs() {
		return images()->clearThumbs($this);
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
