<?php

<<<<<<< master
use hypeJunction\Images\ImageService;
use Imagine\Gd\Imagine;

$plugin_root = __DIR__;
if (file_exists("{$plugin_root}/vendor/autoload.php")) {
	// check if composer dependencies are distributed with the plugin
	require_once "{$plugin_root}/vendor/autoload.php";
}

require_once __DIR__  . '/lib/api.php';

/**
 * Returns an ImageService singleton
 *
 * @staticvar hypeJunction\Images\ImageService $instance
 * @return ImageService
 * @access private
=======
$plugin_root = __DIR__;
if (file_exists("{$plugin_root}/vendor/autoload.php")) {
	require_once "{$plugin_root}/vendor/autoload.php";
}

/**
 * Returns an ImageService singleton
 *
 * @return \hypeJunction\Images\ImageService
>>>>>>> migrate/elgg-7.x
 */
function images() {
	static $instance;
	if (!isset($instance)) {
		$request = _elgg_services()->request;
<<<<<<< master
		$imagine = new Imagine();
		$instance = new ImageService($request, $imagine);
	}
	return $instance;
}
=======
		$imagine = new \Imagine\Gd\Imagine();
		$instance = new \hypeJunction\Images\ImageService($request, $imagine);
	}

	return $instance;
}
>>>>>>> migrate/elgg-7.x
