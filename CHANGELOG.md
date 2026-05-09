<<<<<<< master
<a name="1.1.4"></a>
## [1.1.4](https://github.com/hypeJunction/Elgg-images/compare/1.1.3...v1.1.4) (2016-01-06)


### Bug Fixes

* **upload:** return original file if no file has been uploaded ([0817021](https://github.com/hypeJunction/Elgg-images/commit/0817021))



<a name="1.1.3"></a>
## [1.1.3](https://github.com/hypeJunction/Elgg-images/compare/1.0.0...v1.1.3) (2016-01-06)


### Bug Fixes

* **api:** add missing variable ([00bfee4](https://github.com/hypeJunction/Elgg-images/commit/00bfee4))
* **api:** do not cast file object to bool ([bac8002](https://github.com/hypeJunction/Elgg-images/commit/bac8002))
* **composer:** let composer resolve proxy version based on project config ([2fa9c3b](https://github.com/hypeJunction/Elgg-images/commit/2fa9c3b))
* **cropping:** update cropping logic ([8bc7a31](https://github.com/hypeJunction/Elgg-images/commit/8bc7a31))
* **icons:** delete thumbs on entity delete ([3aef504](https://github.com/hypeJunction/Elgg-images/commit/3aef504))
* **icons:** delete thumbs on entity delete ([7eb40bf](https://github.com/hypeJunction/Elgg-images/commit/7eb40bf))
* **thumbs:** add extension to hook parameters ([66dccc5](https://github.com/hypeJunction/Elgg-images/commit/66dccc5))
* **thumbs:** catch all exceptions ([f040819](https://github.com/hypeJunction/Elgg-images/commit/f040819))
* **thumbs:** consider image unsaved if cropping fails ([c2f07c9](https://github.com/hypeJunction/Elgg-images/commit/c2f07c9))
* **uploads:** check instance before proceeding with upload ([06c7c5d](https://github.com/hypeJunction/Elgg-images/commit/06c7c5d))

### Features

* **images:** add an image service and moves all UI into a separate plugin ([2305037](https://github.com/hypeJunction/Elgg-images/commit/2305037))
* **images:** add an image service and moves all UI into a separate plugin ([30af41b](https://github.com/hypeJunction/Elgg-images/commit/30af41b))



<a name="1.1.2"></a>
## [1.1.2](https://github.com/hypeJunction/Elgg-images/compare/1.1.1...v1.1.2) (2015-12-29)


### Bug Fixes

* **composer:** let composer resolve proxy version based on project config ([2fa9c3b](https://github.com/hypeJunction/Elgg-images/commit/2fa9c3b))



<a name="1.1.1"></a>
## [1.1.1](https://github.com/hypeJunction/Elgg-images/compare/1.1.0...v1.1.1) (2015-12-29)


### Bug Fixes

* **api:** add missing variable ([00bfee4](https://github.com/hypeJunction/Elgg-images/commit/00bfee4))
* **api:** do not cast file object to bool ([bac8002](https://github.com/hypeJunction/Elgg-images/commit/bac8002))



<a name="1.1.0"></a>
# [1.1.0](https://github.com/hypeJunction/Elgg-images/compare/1.0.2...v1.1.0) (2015-12-29)


### Features

* **images:** add an image service and moves all UI into a separate plugin ([2305037](https://github.com/hypeJunction/Elgg-images/commit/2305037))
* **images:** add an image service and moves all UI into a separate plugin ([30af41b](https://github.com/hypeJunction/Elgg-images/commit/30af41b))



<a name="1.2.0"></a>
# [1.2.0](https://github.com/hypeJunction/Elgg-images/compare/1.0.2...v1.2.0) (2015-12-29)


### Features

* **images:** add an image service and moves all UI into a separate plugin ([30af41b](https://github.com/hypeJunction/Elgg-images/commit/30af41b))



<a name="1.1.0"></a>
# [1.1.0](https://github.com/hypeJunction/Elgg-images/compare/1.0.2...v1.1.0) (2015-12-29)


### Features

* **images:** add an image service and moves all UI into a separate plugin ([30af41b](https://github.com/hypeJunction/Elgg-images/commit/30af41b))



<a name="1.0.2"></a>
## [1.0.2](https://github.com/hypeJunction/Elgg-images/compare/1.0.1...v1.0.2) (2015-12-27)


### Bug Fixes

* **cropping:** update cropping logic ([8bc7a31](https://github.com/hypeJunction/Elgg-images/commit/8bc7a31))
* **icons:** delete thumbs on entity delete ([3aef504](https://github.com/hypeJunction/Elgg-images/commit/3aef504))
* **icons:** delete thumbs on entity delete ([7eb40bf](https://github.com/hypeJunction/Elgg-images/commit/7eb40bf))



<a name="1.0.1"></a>
## [1.0.1](https://github.com/hypeJunction/Elgg-images/compare/1.0.0...v1.0.1) (2015-12-25)


### Bug Fixes

* **thumbs:** add extension to hook parameters ([66dccc5](https://github.com/hypeJunction/Elgg-images/commit/66dccc5))
* **thumbs:** catch all exceptions ([f040819](https://github.com/hypeJunction/Elgg-images/commit/f040819))
* **thumbs:** consider image unsaved if cropping fails ([c2f07c9](https://github.com/hypeJunction/Elgg-images/commit/c2f07c9))



<a name="1.0.0"></a>
# 1.0.0 (2015-12-16)


### Bug Fixes

* **screenshots:** fix typo in folder name ([2bd5a10](https://github.com/hypeJunction/Elgg-images/commit/2bd5a10))

### Features

* **releases:** initial commit ([0d9b08b](https://github.com/hypeJunction/Elgg-images/commit/0d9b08b))



=======
# Changelog

## 7.0.0 (2026-05-09)

### Breaking Changes

- Requires Elgg 7.x (`~7.0.0`) and PHP 8.3+

### Changed

- `elgg/elgg ~7.0.0`, PHP `>=8.3`
- Added docker/elgg7/ test stack (PHP 8.3, MySQL 8.0)
- No PHP or CSS breaking changes. No data migration required.

## 6.0.0 (2026-05-09)

### Breaking Changes

- Requires Elgg 6.x (`~6.1.0`) and PHP 8.1+

### Changed

- `Bootstrap` simplified to extend `DefaultPluginBootstrap`
- `ImageService::crop()`: replaced direct metadata access with `getIconCoordinates()` API
- Removed stale `unset($entity->icontime)` (handled by Elgg core)
- Added `ext-intl` to composer.json requirements
- Added docker/elgg6/ test stack

## 5.0.0 (2026-04-24)

### Breaking Changes

- Requires Elgg 5.x (`~5.1.0`) and PHP 8.2+
- `entity:icon:url` is now registered as an event handler (was plugin hook handler)
- `create` event handler no longer returns `false` on thumb failure — creation proceeds regardless

### Changed

- Replaced all `elgg_trigger_plugin_hook()` calls with `elgg_trigger_event_results()`
- Replaced `elgg_register_plugin_hook_handler()` with `elgg_register_event_handler()` for `entity:icon:url`
- Updated `\Elgg\Hook` type hint to `\Elgg\Event` in icon URL handler

## 4.0.0 (2026-04-19)

### Breaking Changes

- Requires Elgg 4.x and PHP 7.4+
- Replaced removed `ElggFile::detectMimeType()` static call with instance method
>>>>>>> migrate/elgg-7.x
