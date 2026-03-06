<?php
require __DIR__ . '/includes/auth.php';

$githubUrl = 'https://github.com/';
$currentUser = openbit_auth_user();
$flashMessage = openbit_flash_get();

if ($currentUser) {
    header('Location: index.php');
    exit;
}

$email = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if (openbit_auth_login($email, $password, $error)) {
        openbit_flash_set('Welcome back to OpenBit.');
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenBit | Login</title>
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
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
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
                <a href="register.php" class="rounded-lg bg-cyan-600 px-3 py-2 text-white transition hover:bg-cyan-500">Register</a>
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

    <main class="mx-auto flex max-w-6xl px-4 py-10 sm:px-6">
        <section class="mx-auto w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-6 sm:p-8">
            <h1 class="text-2xl font-bold text-white">Sign in to OpenBit</h1>
            <p class="mt-2 text-sm text-slate-400">Enter your account details to continue.</p>

            <?php if ($flashMessage): ?>
                <div class="mt-4 rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    <?= htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="mt-4 rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="post" class="mt-6 space-y-4">
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-slate-300">Email</label>
                    <input id="email" name="email" type="email" required value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 outline-none placeholder:text-slate-500 focus:border-cyan-500" placeholder="you@example.com">
                </div>
                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-slate-300">Password</label>
                    <input id="password" name="password" type="password" required class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 outline-none placeholder:text-slate-500 focus:border-cyan-500" placeholder="Your password">
                </div>
                <button type="submit" class="w-full rounded-xl bg-cyan-600 px-4 py-3 font-semibold text-white transition hover:bg-cyan-500">Login</button>
            </form>

            <p class="mt-5 text-sm text-slate-400">
                No account yet?
                <a href="register.php" class="font-medium text-cyan-300 hover:text-cyan-200">Create one</a>
            </p>
        </section>
    </main>
</body>
</html>
