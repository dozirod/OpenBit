# OpenBit

OpenBit is a clean, dark-themed torrent listing website built with PHP and Tailwind CSS.

## Features

- Landing page with project overview
- Category pages for `Games` and `Software`
- Search by file name
- File size formatting (B/KB/MB/GB)
- Direct download button for each file
- Pagination with pages under `pages/`
- Authentication system (register, login, logout)
- CAPTCHA on registration (simple math challenge)
- SQLite-based user storage
- SVG favicon support (`favicon.svg`)

## Project Structure

```text
openbit/
|- index.php
|- games.php
|- software.php
|- login.php
|- register.php
|- logout.php
|- favicon.svg
|- includes/
|  |- auth.php
|  |- catalog.php
|  `- db.php
|- pages/
|  |- games/
|  |  `- index.php
|  `- software/
|     `- index.php
|- data/
|  `- openbit.sqlite
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
7. User accounts are stored in `data/openbit.sqlite`.
8. Database schema is created automatically on first use.

## Requirements

- PHP 8.0+ (recommended)
- Apache, Nginx, or PHP built-in server
- At least one SQLite extension enabled:
  - `pdo_sqlite` (preferred), or
  - `sqlite3`

## Run Locally

```bash
php -S localhost:8000
```

Open in browser:

```text
http://localhost:8000
```

## Enable SQLite Extensions (Windows)

If login/register shows database driver errors, enable SQLite in `php.ini`:

```ini
extension=sqlite3
extension=pdo_sqlite
```

Then restart the PHP server.

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
