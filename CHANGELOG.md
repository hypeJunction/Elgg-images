# Changelog

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
