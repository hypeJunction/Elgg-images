# images — Architecture (Elgg 7.x)

## Summary

Pure service plugin: image upload, manipulation, and thumbnail generation for any `ElggFile`-based entity. No routes, views, or actions — other plugins consume it via `images()` helper and event filters.

## Directory Structure

```
images/
├── autoloader.php             # loads vendor/autoload.php if present
├── composer.json              # PHP >=8.2, elgg/elgg ~5.1.0, imagine/imagine ^1.0
├── elgg-plugin.php            # bootstrap only
├── classes/hypeJunction/Images/
│   ├── Bootstrap.php          # registers entity:icon:url, create, update:after, delete events
│   ├── Image.php              # ElggFile subclass with thumb helpers
│   ├── ImageInterface.php     # interface for Image
│   ├── ImageService.php       # core upload/thumb/crop logic (Imagine\Gd)
│   ├── Thumb.php              # ElggFile subclass for thumbnails
│   └── ThumbInterface.php     # (empty placeholder)
└── docker/                    # Elgg 5.x per-plugin test stack
```

## Registered Events

All registered imperatively in `Bootstrap::init()`.

| Event | Type | Purpose |
|-------|------|---------|
| `entity:icon:url` | `object` | Returns inline URL of the sized thumbnail when entity is an image |
| `create` | `object` | Generates thumbnails when an image entity is created |
| `update:after` | `object` | Regenerates thumbnails when an image entity is updated |
| `delete` | `object` | Clears thumbnails when an image entity is deleted (priority 999) |

## Filterable Events (formerly plugin hooks)

Consumers can filter behaviour via `elgg_register_event_handler()`:

| Event | Type | Default | Purpose |
|-------|------|---------|---------|
| `directory` | `object` | `'file'` | Override file storage directory |
| `thumb:filename` | `object` | `'{guid}/{size}.{ext}'` | Override thumb filename |
| `thumb:sizes` | `object` | `elgg_get_config('icon_sizes')` | Override thumb size map |
| `thumb:directory` | `object` | `'icons'` | Override thumb storage directory |
| `options` | `imagine` | `[]` | Pass Imagine save options (quality, etc.) |

## Key Classes

**`ImageService`** — singleton returned by `images()`. Constructor receives `Elgg\Http\Request` and `Imagine\Image\ImagineInterface` (GD backend). Key methods:
- `createFromUpload($input_name, ElggFile)` — saves an uploaded image file
- `createFromResource($path, ElggFile)` — saves an image from URL/path
- `createThumbs(ElggEntity, x1, y1, x2, y2)` — generates all registered sizes
- `clearThumbs(ElggEntity)` — deletes all thumbnails
- `getThumb(ElggEntity, $size)` — returns `Thumb` entity for given size
- `isImage($entity)` — checks MIME type (jpeg/gif/png only)

## Dependencies

- `imagine/imagine ^1.0` — image processing (GD backend)
- No other plugin dependencies

## Migration Notes (6.x → 7.x)

- `elgg/elgg ~7.0.0`, `php >=8.3` in `composer.json`.
- Docker test stack added for Elgg 7.x (docker/elgg7/) with PHP 8.3.
- No breaking changes: no CSS Crush syntax, no direct `ElggObject` instantiation, no removed Elgg APIs.
- No data migration needed.

## Migration Notes (5.x → 6.x)

- `elgg/elgg ~6.1.0`, `php >=8.1`, `ext-intl` added in `composer.json`.
- `Bootstrap` simplified to extend `DefaultPluginBootstrap` (no-op method stubs removed).
- `ImageService::crop()`: replaced direct metadata access `$entity->x1/y1/x2/y2` with `getIconCoordinates()`.
- Removed stale `unset($entity->icontime)` call (handled by Elgg core since 4.x).
- Docker test stack added for Elgg 6.x (docker/elgg6/).
- No data migration needed.

## Migration Notes (4.x → 5.x)

- `elgg_register_plugin_hook_handler` → `elgg_register_event_handler` for `entity:icon:url`
- `\Elgg\Hook` → `\Elgg\Event` in the icon URL handler
- `elgg_trigger_plugin_hook` → `elgg_trigger_event_results` across all 6 call sites in `ImageService`
- Removed `return false` from `create` and `update:after` handlers — in 5.x these events cannot prevent entity creation/update
- PHP minimum bumped to `>=8.2`
