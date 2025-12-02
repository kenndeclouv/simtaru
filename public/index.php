<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createMutable(__DIR__ . '/../');
    $dotenv->safeLoad();

    $subdir = $_ENV['SUBDIR_PATH'] ?? null;

    if (!empty($subdir) && isset($_SERVER['REQUEST_URI']) && str_starts_with($_SERVER['REQUEST_URI'], $subdir)) {
        $newUri = substr($_SERVER['REQUEST_URI'], strlen($subdir));
        $_SERVER['REQUEST_URI'] = $newUri ?: '/';
    }
} catch (\Exception $e) {}

(require_once __DIR__ . '/../bootstrap/app.php')
    ->handleRequest(Request::capture());
