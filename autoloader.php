<?php

use hypeJunction\Images\ImageService;
use Imagine\Gd\Imagine;

$plugin_root = __DIR__;
if (file_exists("{$plugin_root}/vendor/autoload.php")) {
	// check if composer dependencies are distributed with the plugin
	require_once "{$plugin_root}/vendor/autoload.php";
}

/**
 * Returns an ImageService singleton
 *
 * @staticvar hypeJunction\Images\ImageService $instance
 * @return ImageService
 */
function images() {
	static $instance;
	if (!isset($instance)) {
		$request = _elgg_services()->request;
		$imagine = new Imagine();
		$instance = new ImageService($request, $imagine);
	}
	return $instance;
}