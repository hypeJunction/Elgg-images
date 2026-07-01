<?php

$plugin_root = __DIR__;
if (file_exists("{$plugin_root}/vendor/autoload.php")) {
	require_once "{$plugin_root}/vendor/autoload.php";
}

/**
 * Returns an ImageService singleton
 *
 * @return \hypeJunction\Images\ImageService
 */
function images() {
	static $instance;
	if (!isset($instance)) {
		$request = _elgg_services()->request;
		$imagine = new \Imagine\Gd\Imagine();
		$instance = new \hypeJunction\Images\ImageService($request, $imagine);
	}

	return $instance;
}
