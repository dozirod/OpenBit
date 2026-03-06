<?php
require __DIR__ . '/includes/auth.php';

$githubUrl = 'https://github.com/';
$currentUser = openbit_auth_user();
$flashMessage = openbit_flash_get();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenBit</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
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
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -left-24 top-16 h-80 w-80 rounded-full bg-cyan-500/10 blur-3xl"></div>
        <div class="absolute -right-24 bottom-10 h-96 w-96 rounded-full bg-indigo-500/10 blur-3xl"></div>
    </div>
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
            <a href="index.php" class="text-xl font-semibold tracking-wide text-white">OpenBit</a>
            <nav class="flex items-center gap-2 sm:gap-3 text-sm">
                <a href="games.php" class="rounded-lg px-3 py-2 text-slate-300 transition hover:bg-slate-800 hover:text-white">Games</a>
                <a href="software.php" class="rounded-lg px-3 py-2 text-slate-300 transition hover:bg-slate-800 hover:text-white">Software</a>
                <?php if ($currentUser): ?>
                    <span class="hidden rounded-lg border border-slate-700 px-3 py-2 text-slate-300 sm:inline">Hi, <?= htmlspecialchars((string)$currentUser['display_name'], ENT_QUOTES, 'UTF-8') ?></span>
                    <a href="logout.php" class="rounded-lg border border-slate-700 px-3 py-2 text-slate-200 transition hover:bg-slate-800 hover:text-white">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="rounded-lg border border-slate-700 px-3 py-2 text-slate-200 transition hover:bg-slate-800 hover:text-white">Login</a>
                    <a href="register.php" class="rounded-lg bg-cyan-600 px-3 py-2 text-white transition hover:bg-cyan-500">Register</a>
                <?php endif; ?>
                <a
                    href="<?= htmlspecialchars($githubUrl, ENT_QUOTES, 'UTF-8') ?>"
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

    <main class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <?php if ($flashMessage): ?>
            <section class="mb-6 rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                <?= htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8') ?>
            </section>
        <?php endif; ?>

        <section class="relative overflow-hidden rounded-2xl border border-slate-800 bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 p-8 sm:p-10">
            <div class="absolute -right-16 -top-16 h-44 w-44 rounded-full border border-cyan-400/20 bg-cyan-500/10 blur-2xl"></div>
            <div class="absolute -bottom-16 -left-16 h-52 w-52 rounded-full border border-indigo-400/20 bg-indigo-500/10 blur-2xl"></div>
            <p class="mb-4 inline-flex rounded-full border border-cyan-600/40 bg-cyan-600/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-cyan-300">
                Decentralized Downloads
            </p>
            <h1 class="max-w-3xl text-3xl font-bold tracking-tight text-white sm:text-5xl">
                OpenBit is your modern torrent hub for games and software
            </h1>
            <p class="mt-5 max-w-3xl text-slate-300 sm:text-lg">
                Discover curated files, search instantly by name, and download what you need without clutter.
                OpenBit keeps the experience fast, minimal, and focused, with separate libraries for games and software,
                pagination for large collections, and clean dark-mode visuals for long browsing sessions.
            </p>
            <p class="mt-4 max-w-3xl text-slate-400">
                Create an account to save favorites, track downloads, and keep your own personal library organized.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <?php if (!$currentUser): ?>
                    <a href="register.php" class="rounded-xl bg-cyan-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-cyan-500">Create Account</a>
                    <a href="login.php" class="rounded-xl border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-100 transition hover:bg-slate-800">Sign In</a>
                <?php endif; ?>
                <a href="games.php" class="rounded-xl border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">Explore Library</a>
            </div>
        </section>

        <section class="mt-6 grid gap-4 md:grid-cols-3">
            <article class="rounded-xl border border-slate-800 bg-slate-900/80 p-5">
                <h2 class="text-lg font-semibold text-white">Smart Categories</h2>
                <p class="mt-2 text-sm text-slate-400">Content is split between <code>games</code> and <code>software</code>, so navigation stays clear and fast.</p>
            </article>
            <article class="rounded-xl border border-slate-800 bg-slate-900/80 p-5">
                <h2 class="text-lg font-semibold text-white">Quick Search</h2>
                <p class="mt-2 text-sm text-slate-400">Find files instantly with built-in filtering by file name on every category page.</p>
            </article>
            <article class="rounded-xl border border-slate-800 bg-slate-900/80 p-5">
                <h2 class="text-lg font-semibold text-white">Direct Downloads</h2>
                <p class="mt-2 text-sm text-slate-400">See file sizes clearly and start downloads in one click from a clean table interface.</p>
            </article>
        </section>

        <section class="mt-6 grid gap-4 sm:grid-cols-2">
            <a href="games.php" class="rounded-xl border border-slate-700 bg-slate-900/70 p-5 transition hover:border-cyan-500 hover:bg-slate-900">
                <h2 class="text-lg font-medium text-white">Games</h2>
                <p class="mt-2 text-sm text-slate-400">Browse torrents from <code>downloads/games/</code>.</p>
            </a>
            <a href="software.php" class="rounded-xl border border-slate-700 bg-slate-900/70 p-5 transition hover:border-cyan-500 hover:bg-slate-900">
                <h2 class="text-lg font-medium text-white">Software</h2>
                <p class="mt-2 text-sm text-slate-400">Browse torrents from <code>downloads/software/</code>.</p>
            </a>
        </section>
    </main>
</body>
</html>
