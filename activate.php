<?php

require_once __DIR__ . '/autoloader.php';

$subtypes = [
	'image' => hypeJunction\Images\Image::class,
];

foreach ($subtypes as $subtype => $class) {
	if (!update_subtype('object', $subtype, $class)) {
		add_subtype('object', $subtype, $class);
	}
}