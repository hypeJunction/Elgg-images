<?php

elgg_push_context('images/all');

$container_guid = elgg_extract('container_guid', $vars);
if (!$container_guid) {
	$container_guid = elgg_get_logged_in_user_guid();
}

$container = get_entity($container_guid);
if (!$container) {
	forward('', '404');
}

elgg_set_page_owner_guid($container->guid);

elgg_group_gatekeeper();

if ($container instanceof ElggUser) {
	$owner_guid = $container->guid;
	$container_guid = ELGG_ENTITIES_ANY_VALUE;
	$title = elgg_echo('images:by', [$container->getDisplayName()]);
} else {
	$owner_guid = ELGG_ENTITIES_ANY_VALUE;
	$title = elgg_echo('images:in', [$container->getDisplayName()]);
}

if ($container->canWriteToContainer(0, 'object', 'image')) {
	elgg_register_menu_item('title', [
		'name' => 'add',
		'text' => elgg_echo('images:upload'),
		'href' => "/images/add/$container->guid",
		'class' => 'elgg-button elgg-button-action',
	]);
}

elgg_push_breadcrumb(elgg_echo('images'), '/images/all');
elgg_push_breadcrumb($container->getDisplayName());

$content = elgg_list_entities([
	'types' => 'object',
	'subtypes' => 'image',
	'owner_guids' => $owner_guid,
	'container_guids' => $container_guid,
	'no_results' => elgg_echo('images:no_results'),
	'size' => 'medium',
]);

$body = elgg_view_layout('content', [
	'content' => $content,
	'title' => $title,
	'filter' => '',
		]);

echo elgg_view_page($title, $body);
