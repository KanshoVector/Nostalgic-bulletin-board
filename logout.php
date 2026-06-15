<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/layout.php';

require_login();
session_destroy();
setcookie(session_name(), '', time() - 42000, '/');
header('Location: login.php');
exit;
