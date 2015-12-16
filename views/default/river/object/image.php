<?php

$item = $vars['item'];
/* @var ElggRiverItem $item */

$object = $item->getObjectEntity();
if (!$object) {
	return;
}

echo elgg_view('river/elements/layout', [
	'item' => $item,
	'attachments' => elgg_view_entity_icon($object, 'media', [
		'use_link' => false,
	]),
]);
