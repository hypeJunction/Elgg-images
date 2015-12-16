<?php

elgg_push_context('images/view');

$guid = elgg_extract('guid', $vars);
$entity = get_entity($guid);

if (!$entity instanceof hypeJunction\Images\Image) {
	forward('', '404');
}

$container = $entity->getContainerEntity();

elgg_set_page_owner_guid($entity->container_guid);

elgg_group_gatekeeper();

elgg_push_breadcrumb(elgg_echo('images'), '/images/all');
if ($container) {
	elgg_push_breadcrumb($container->getDisplayName(), "/images/all/$entity->container_guid");
}

elgg_push_breadcrumb($entity->getDisplayName());

elgg_register_menu_item('title', [
	'name' => 'download',
	'text' => elgg_echo('images:download'),
	'href' => $entity->getDownloadUrl(),
	'class' => 'elgg-button elgg-button-action',
]);

if ($entity->canEdit()) {
	elgg_register_menu_item('title', [
		'name' => 'edit',
		'text' => elgg_echo('edit'),
		'href' => "/images/edit/$entity->guid",
		'class' => 'elgg-button elgg-button-action',
	]);
}

$content = elgg_view_entity($entity, [
	'full_view' => true,
		]);

$body = elgg_view_layout('content', [
	'content' => $content,
	'title' => $entity->getDisplayName(),
	'filter' => '',
	'entity' => $entity,
		]);

echo elgg_view_page($title, $body, 'default', [
	'entity' => $entity,
]);
