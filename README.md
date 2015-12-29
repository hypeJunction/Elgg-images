Image API for Elgg
==================
![Elgg 1.11](https://img.shields.io/badge/Elgg-1.11.x-orange.svg?style=flat-square)
![Elgg 1.12](https://img.shields.io/badge/Elgg-1.12.x-orange.svg?style=flat-square)
![Elgg 2.0](https://img.shields.io/badge/Elgg-2.0.x-orange.svg?style=flat-square)

## Features

 * Generic API for handling image files
 * Standardized handling of thubmnails for all image files

## API

 * `images()->createFromUpload()` - create image file from upload
 * `images()->createFromResource()` - create image file from a file resource
 * `images()->crop()` - crop source image
 * `images()->isImage()` - check if an entity is an image
 * `images()->createThumbs()` - creates thumbnails for an image
 * `images()->getThumb()` - returns a thumbnail file object
 * `images()->clearThumbs()` - removes all image thumbnails

## Hooks

 * `'thumb:sizes',$entity_type` - filters thumnail sizes configuration array
 * `'thumb:directory', $entity_type` - filters directory name in entity owners filestore directory where thumbs will be saved
 * `'thumb:filename', $entity_type` - filters filename that will be given to the thumbnail
 * `'options', 'imagine'` - filters options passed to Imagine, when saving cropped images

## Thumbs config

Thubm sizes can be configured an array of options:

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
