<?php

use hypeJunction\Images\Avatar;

require_once __DIR__ . '/autoloader.php';

$subtypes = [
	Avatar::SUBTYPE => Avatar::class,
];

foreach ($subtypes as $subtype => $class) {
	elgg_set_entity_class('object', $subtype, $class);
}