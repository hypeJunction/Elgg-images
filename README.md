# images

![Elgg 7.x](https://img.shields.io/badge/Elgg-7.x-orange.svg?style=flat-square)

Pure-service API plugin for Elgg that handles image upload, manipulation, and thumbnail generation for any file-based entity.

## Features

- Image upload from HTTP multipart or local path/URL via the `images()` service helper
- Automatic thumbnail generation at all configured icon sizes on entity create/update
- Filterable thumb sizes, filenames, and storage directories via Elgg events
- `entity:icon:url` integration — icon requests resolved to inline thumbnail URLs automatically
- Thumbnail lifecycle management: thumbnails are regenerated on update and cleared on delete

## Installation

**Via Composer (recommended):**

```bash
composer require hypejunction/images
```

**Manual:**

Download the zip, extract into your Elgg `mod/` directory, and activate in the admin panel.

## Compatibility

| Plugin version | Elgg version |
|---|---|
| 7.0.0 | 7.x |
| 6.0.0 | 6.x |
| 5.0.0 | 5.x |
| 4.0.0 | 4.x |
| 3.0.0 | 3.x |

## License

GPL-2.0-or-later
