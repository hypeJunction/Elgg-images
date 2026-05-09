# images

![Elgg 5.x](https://img.shields.io/badge/Elgg-5.x-orange.svg?style=flat-square)

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

## License

GPL-2.0-or-later
