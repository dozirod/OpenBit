# OpenBit

OpenBit is a clean, dark-themed torrent listing website built with PHP and Tailwind CSS.

## Features

- Landing page with project overview
- Category pages for `Games` and `Software`
- Search by file name
- File size formatting (B/KB/MB/GB)
- Direct download button for each file
- Pagination with pages under `pages/`
- Basic `Login` and `Register` UI pages

## Project Structure

```text
openbit/
|- index.php
|- games.php
|- software.php
|- login.php
|- register.php
|- includes/
|  `- catalog.php
|- pages/
|  |- games/
|  |  `- index.php
|  `- software/
|     `- index.php
`- downloads/
   |- games/
   `- software/
```

## How It Works

1. `games.php` loads files from `downloads/games/`.
2. `software.php` loads files from `downloads/software/`.
3. Search uses the `q` query parameter.
4. Results are paginated (12 items per page).
5. Page 1 uses `games.php` and `software.php`.
6. Page 2+ uses:
   - `pages/games/index.php?page=2`
   - `pages/software/index.php?page=2`

## Requirements

- PHP 8.0+ (recommended)
- Apache, Nginx, or PHP built-in server

## Run Locally

```bash
php -S localhost:8000
```

Open in browser:

```text
http://localhost:8000
```

## Add Files

Torrent files are stored in a separate repository (not inside this project).

External torrents repository:

- `https://github.com/your-username/openbit-downloads`

After cloning or syncing that repository, place files into:

- `downloads/games/`
- `downloads/software/`

They will appear automatically in the corresponding category.

## Configuration

Update your GitHub URL in:

- `index.php`
- `includes/catalog.php`
- `login.php`
- `register.php`
