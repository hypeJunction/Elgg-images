Image API for Elgg
==================
![Elgg 1.11](https://img.shields.io/badge/Elgg-1.11.x-orange.svg?style=flat-square)
![Elgg 1.12](https://img.shields.io/badge/Elgg-1.12.x-orange.svg?style=flat-square)
![Elgg 2.0](https://img.shields.io/badge/Elgg-2.0.x-orange.svg?style=flat-square)

## Features

 * Generic API for handling image files and entity avatars
 * Standardized handling of thubmnails for all image files

## API

 * `elgg_imges_create_from_upload()` - create image file from upload
 * `elgg_images_create_from_resource()` - create image file from a file resource
 * `elgg_images_crop()` - crop source image
 * `elgg_images_is_image()` - check if an entity is an image
 * `elgg_images_create_thumbs()` - creates thumbnails for an image
 * `elgg_images_get_thumbs()` - returns a thumbnail file object
 * `elgg_images_clear_thumbs()` - removes all image thumbnails
 * `elgg_images_create_avatar_from_upload()` - create an avatar for an entity from file upload
 * `elgg_images_create_avatar_from_resource()` - create an avatar for an entity from file resource
 * `elgg_images_clear_avatars()` - clear entity avatars
 * `elgg_images_get_avatar()` - get avatar for an entity

## Hooks

 * `'thumb:sizes',$entity_type` - filters thumnail sizes configuration array
 * `'thumb:directory', $entity_type` - filters directory name in entity owners filestore directory where thumbs will be saved
 * `'thumb:filename', $entity_type` - filters filename that will be given to the thumbnail
 * `'options', 'imagine'` - filters options passed to Imagine, when saving cropped images

## Thumbs config

Thubm sizes can be configured as an array of options:

```php
// Add `media` size that will be cropped in an `outbound` mode filling a 640x360 container.
$thumbs['media'] = [
	'w' => 640, // max width
	'h' => 360, // max height
	'square' => false, // crop a square
	'croppable' => true, // allow cropping
	'mode' => 'outbound', // 'inset' or 'outbound'
];
```
By default, all square thumbs will be cropped in `outbound` mode.
`master` size will be crooped in `inset` mode without cropping.

## Notes

* This plugin distinguishes between thumbs and avatars. Thumbs are resized instances of the ElggFile entity, whereas avatars
are ElggFile entities contained by the entity they belong to. Avatars belong to non file entities and have thumbs.

* If you override file plugin thumbs, you will need to update the file upload action to remove the code that generates thumbnails.
Otherwise, two sets of thumbs will be generated. Currently there is no way to non-intrusively override thumb generation in the file plugin.