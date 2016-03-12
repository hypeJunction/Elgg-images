<?php

use hypeJunction\Images\Avatar;

require_once __DIR__ . '/autoloader.php';

$subtypes = [
	Avatar::SUBTYPE => Avatar::class,
];

foreach ($subtypes as $subtype => $class) {
	if (!update_subtype('object', $subtype, $class)) {
		add_subtype('object', $subtype, $class);
	}
}