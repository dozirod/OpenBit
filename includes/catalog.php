<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if (!function_exists('openbit_h')) {
    function openbit_h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('openbit_format_size')) {
    function openbit_format_size(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
}

if (!function_exists('openbit_page_url')) {
    function openbit_page_url(string $category, int $page, string $basePrefix, string $search): string
    {
        $query = $search !== '' ? '?q=' . rawurlencode($search) : '';
        $queryWithPage = '?page=' . $page . ($search !== '' ? '&q=' . rawurlencode($search) : '');

        if ($page <= 1) {
            return $basePrefix . $category . '.php' . $query;
        }

        return $basePrefix . 'pages/' . $category . '/index.php' . $queryWithPage;
    }
}

if (!function_exists('openbit_render_catalog_page')) {
    function openbit_render_catalog_page(string $category, int $page, string $basePrefix): void
    {
        $categories = [
            'games' => 'Games',
            'software' => 'Software',
        ];

        if (!isset($categories[$category])) {
            http_response_code(404);
            echo 'Unknown category.';
            return;
        }

        $currentPage = max(1, $page);
        $perPage = 12;
        $search = trim((string)($_GET['q'] ?? ''));
        $baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'downloads';
        $categoryDir = $baseDir . DIRECTORY_SEPARATOR . $category;
        $downloadPrefix = $basePrefix;
        $githubUrl = 'https://github.com/';
        $currentUser = openbit_auth_user();

        $allFiles = [];
        $dirReadable = is_dir($categoryDir) && is_readable($categoryDir);

        if ($dirReadable) {
            foreach (scandir($categoryDir) ?: [] as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }

                $fullPath = $categoryDir . DIRECTORY_SEPARATOR . $entry;
                if (!is_file($fullPath)) {
                    continue;
                }

                if ($search !== '' && stripos($entry, $search) === false) {
                    continue;
                }

                $allFiles[] = [
                    'name' => $entry,
                    'size' => filesize($fullPath) ?: 0,
                    'relative' => $downloadPrefix . 'downloads/' . rawurlencode($category) . '/' . rawurlencode($entry),
                ];
            }

            usort($allFiles, static fn(array $a, array $b): int => strcasecmp($a['name'], $b['name']));
        }

        $totalFiles = count($allFiles);
        $totalPages = max(1, (int)ceil($totalFiles / $perPage));
        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $offset = ($currentPage - 1) * $perPage;
        $files = array_slice($allFiles, $offset, $perPage);

        $title = 'OpenBit | ' . $categories[$category];

        ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= openbit_h($title) ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= openbit_h($basePrefix . 'favicon.svg') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Sora', 'ui-sans-serif', 'system-ui', 'sans-serif']
                    }
                }
            }
        };
    </script>
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -left-24 top-16 h-80 w-80 rounded-full bg-cyan-500/10 blur-3xl"></div>
        <div class="absolute -right-24 bottom-10 h-96 w-96 rounded-full bg-indigo-500/10 blur-3xl"></div>
    </div>
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
            <a href="<?= openbit_h($basePrefix . 'index.php') ?>" class="text-xl font-semibold tracking-wide text-white">OpenBit</a>
            <nav class="flex items-center gap-2 sm:gap-3 text-sm">
                <?php foreach ($categories as $key => $label): ?>
                    <?php $active = $category === $key; ?>
                    <a
                        href="<?= openbit_h($basePrefix . $key . '.php') ?>"
                        class="rounded-lg px-3 py-2 transition <?= $active ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>"
                    >
                        <?= openbit_h($label) ?>
                    </a>
                <?php endforeach; ?>
                <?php if ($currentUser): ?>
                    <span class="hidden rounded-lg border border-slate-700 px-3 py-2 text-slate-300 sm:inline">Hi, <?= openbit_h((string)$currentUser['display_name']) ?></span>
                    <a href="<?= openbit_h($basePrefix . 'logout.php') ?>" class="rounded-lg border border-slate-700 px-3 py-2 text-slate-200 transition hover:bg-slate-800 hover:text-white">Logout</a>
                <?php else: ?>
                    <a href="<?= openbit_h($basePrefix . 'login.php') ?>" class="rounded-lg border border-slate-700 px-3 py-2 text-slate-200 transition hover:bg-slate-800 hover:text-white">Login</a>
                    <a href="<?= openbit_h($basePrefix . 'register.php') ?>" class="rounded-lg bg-cyan-600 px-3 py-2 text-white transition hover:bg-cyan-500">Register</a>
                <?php endif; ?>
                <a
                    href="<?= openbit_h($githubUrl) ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-slate-300 transition hover:bg-slate-800 hover:text-white"
                    aria-label="GitHub Source Code"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                        <path d="M12 .5a12 12 0 0 0-3.79 23.39c.6.11.82-.26.82-.58v-2.02c-3.34.73-4.04-1.61-4.04-1.61-.55-1.39-1.33-1.76-1.33-1.76-1.08-.74.08-.72.08-.72 1.2.08 1.83 1.23 1.83 1.23 1.06 1.82 2.78 1.29 3.46.99.11-.77.42-1.29.76-1.58-2.67-.3-5.47-1.34-5.47-5.95 0-1.31.47-2.38 1.23-3.22-.12-.3-.53-1.52.12-3.16 0 0 1.01-.32 3.31 1.23A11.5 11.5 0 0 1 12 6.31a11.5 11.5 0 0 1 3.02.41c2.3-1.55 3.31-1.23 3.31-1.23.65 1.64.24 2.86.12 3.16.76.84 1.23 1.91 1.23 3.22 0 4.62-2.8 5.65-5.48 5.95.43.37.81 1.1.81 2.22v3.29c0 .32.22.7.83.58A12 12 0 0 0 12 .5Z"/>
                    </svg>
                    <span class="hidden sm:inline">GitHub</span>
                </a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        <section class="mb-6 rounded-2xl border border-slate-800 bg-gradient-to-br from-slate-900 to-slate-950 p-5 sm:p-6">
            <p class="inline-flex rounded-full border border-cyan-600/40 bg-cyan-600/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-cyan-300">
                <?= openbit_h($categories[$category]) ?> Library
            </p>
            <h1 class="mt-3 text-2xl font-bold text-white sm:text-3xl"><?= openbit_h($categories[$category]) ?> Torrents</h1>
            <p class="mt-2 text-sm text-slate-400 sm:text-base">
                Browse files from <code>downloads/<?= openbit_h($category) ?>/</code>, search by name, and download instantly.
            </p>
        </section>

        <section class="mb-6 rounded-xl border border-slate-800 bg-slate-900 p-4 sm:p-5">
            <form method="get" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <?php if ($currentPage > 1): ?>
                    <input type="hidden" name="page" value="<?= $currentPage ?>">
                <?php endif; ?>
                <label for="search" class="sr-only">Search files</label>
                <input
                    id="search"
                    name="q"
                    value="<?= openbit_h($search) ?>"
                    type="text"
                    placeholder="Search torrents by file name..."
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-2.5 text-slate-100 outline-none placeholder:text-slate-500 focus:border-cyan-500"
                >
                <button
                    type="submit"
                    class="rounded-lg bg-cyan-600 px-4 py-2.5 font-medium text-white transition hover:bg-cyan-500"
                >
                    Search
                </button>
            </form>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-800/80 text-left text-slate-300">
                        <tr>
                            <th class="px-4 py-3 font-medium">File Name</th>
                            <th class="px-4 py-3 font-medium">Size</th>
                            <th class="px-4 py-3 font-medium text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        <?php if (!$dirReadable): ?>
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-slate-400">
                                    Folder not found or not readable: <code class="text-slate-300">downloads/<?= openbit_h($category) ?>/</code>
                                </td>
                            </tr>
                        <?php elseif ($totalFiles === 0): ?>
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-slate-400">
                                    No files found<?= $search !== '' ? ' for "' . openbit_h($search) . '"' : '' ?>.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($files as $file): ?>
                                <tr class="transition hover:bg-slate-800/50">
                                    <td class="px-4 py-3 align-middle">
                                        <span class="break-all"><?= openbit_h($file['name']) ?></span>
                                    </td>
                                    <td class="px-4 py-3 align-middle text-slate-300"><?= openbit_h(openbit_format_size((int)$file['size'])) ?></td>
                                    <td class="px-4 py-3 text-right align-middle">
                                        <a
                                            href="<?= openbit_h($file['relative']) ?>"
                                            class="inline-block rounded-lg bg-emerald-600 px-3 py-2 font-medium text-white transition hover:bg-emerald-500"
                                            download
                                        >
                                            Download
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <?php if ($totalFiles > 0 && $totalPages > 1): ?>
            <section class="mt-6 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-800 bg-slate-900 px-4 py-3 text-sm">
                <p class="text-slate-400">Page <?= $currentPage ?> of <?= $totalPages ?></p>
                <div class="flex flex-wrap gap-2">
                    <?php if ($currentPage > 1): ?>
                        <a href="<?= openbit_h(openbit_page_url($category, $currentPage - 1, $basePrefix, $search)) ?>" class="rounded-lg border border-slate-700 px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white">Prev</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a
                            href="<?= openbit_h(openbit_page_url($category, $i, $basePrefix, $search)) ?>"
                            class="rounded-lg border px-3 py-2 <?= $i === $currentPage ? 'border-cyan-500 bg-cyan-600 text-white' : 'border-slate-700 text-slate-300 hover:bg-slate-800 hover:text-white' ?>"
                        >
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="<?= openbit_h(openbit_page_url($category, $currentPage + 1, $basePrefix, $search)) ?>" class="rounded-lg border border-slate-700 px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white">Next</a>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
<?php
    }
}
