<?php
require __DIR__ . '/includes/auth.php';

openbit_auth_logout();
openbit_flash_set('You have been logged out.');

header('Location: index.php');
exit;
