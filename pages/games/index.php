<?php
declare(strict_types=1);

require dirname(__DIR__, 2) . '/includes/catalog.php';
$page = (int)($_GET['page'] ?? 2);
if ($page < 2) {
    $page = 2;
}

openbit_render_catalog_page('games', $page, '../../');
