<?php

declare(strict_types=1);

// Change this if your folder name in htdocs is different
define('APP_NAME', 'Complaint Management System');
define('BASE_URL', '/complaint-management');

function app_url(string $path = ''): string
{
    $path = ltrim($path, '/');

    return BASE_URL . ($path !== '' ? '/' . $path : '');
}
