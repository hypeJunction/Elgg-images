<?php

use hypeJunction\Images\Thumb;

/**
 * Write uploaded file to a file object
 * If no $file is provided, a new object of subtype 'file' will be created
 *
 * @param string   $input_name Input name
 * @param ElggFile $file       Optional file object to write to
 * @return ElggFile|false
 */
function elgg_images_create_from_upload($input_name, ElggFile $file = null) {
	return images()->createFromUpload($input_name, $file);
}

/**
 * Write a file resource into a file object
 * If no $file is provided, a new object of subtype 'file' will be created
 *
 * @param string   $path Full path to file
 * @param ElggFile $file Optional file object to write to
 * @return ElggFile|false
 */
function elgg_images_create_from_resource($path, ElggFile $file = null) {
	return images()->createFromResource($path, $file);
}

/**
 * Check if an entity is an image, and if this plugin is allowed to treat it as one
 *
 * @param ElggFile $entity File entity
 * @return bool
 */
function elgg_images_is_image($entity = null) {
	return images()->isImage($entity);
}

/**
 * Retrieve a thumbnail image of an image file
 *
 * @param ElggEntity $entity Entity
 * @param string     $size   Thumb size
 * @return boolean|Thumb
 */
function elgg_images_get_thumb(ElggEntity $entity, $size = 'medium') {
	return images()->getThumb($entity, $size);
}

/**
 * Get thumbs sizes config
 *
 * @param ElggEntity $entity Entity
 * @return array
 */
function elgg_images_get_thumb_sizes(ElggEntity $entity) {
	return images()->getThumbSizes($entity);
}

/**
 * Crop source image
 *
 * @param ElggEntity $entity  Entity
 * @param int        $x1 Upper left crooping coordinate
 * @param int        $y1 Upper left crooping coordinate
 * @param int        $x2 Lower right cropping coordinate
 * @param int        $y2 Lower right cropping coordinate
 * @return bool
 */
function elgg_images_crop(ElggEntity $entity, $x1, $y1, $x2, $y2) {
	return images()->crop($entity, $x1, $y1, $x2, $y2);
}

/**
 * Create image thumbnails
 * If coordinates are not set, $entity metadata will be used
 *
 * @param ElggEntity $entity  Entity
 * @param int        $x1 Upper left crooping coordinate
 * @param int        $y1 Upper left crooping coordinate
 * @param int        $x2 Lower right cropping coordinate
 * @param int        $y2 Lower right cropping coordinate
 * @return Thumb[]|false
 */
function elgg_images_create_thumbs(ElggEntity $entity, $x1 = null, $y1 = null, $x2 = null, $y2 = null) {
	return images()->createThumbs($entity, $x1, $y1, $x2, $y2);
}

/**
 * Remove file thumbs
 *
 * @param ElggEntity $entity Image file entity
 * @return void
 */
function elgg_images_clear_thumbs(ElggEntity $entity) {
	return images()->clearThumbs($entity);
}
