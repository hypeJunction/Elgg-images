<?php

namespace hypeJunction\Images;

/**
 * Image interface
 */
interface ImageInterface {

	/**
	 * Returns an array of thumb sizes
	 * @return array
	 */
	public function getThumbSizes();

	/**
	 * Returns name of the directory in which thumbs are stored
	 * @return string
	 */
	public function getThumbDirectory();

	/**
	 * Returns thumb filename
	 *
	 * @param string $size Size
	 * @return string
	 */
	public function getThumbFilename($size);

	/**
	 * Returns a thumb object
	 *
	 * @param string $size Size
	 * @return ThumbInterface|false
	 */
	public function getThumb($size = 'medium');

	/**
	 * Creates thumbs of this image
	 *
	 * @param int $x1 Cropping coordinate
	 * @param int $y1 Cropping coordinate
	 * @param int $x2 Cropping coordinate
	 * @param int $y2 Cropping coordinate
	 * @return ThumbInterface[]|false
	 */
	public function createThumbs($x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0);

	/**
	 * Clear created thumbs
	 * @return bool
	 */
	public function clearThumbs();

	/**
	 * Returns download URL
	 * @return string
	 */
	public function getDownloadUrl();

	/**
	 * Returns inline display URL
	 * @return string
	 */
	public function getInlineUrl();

}
