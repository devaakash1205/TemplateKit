<?php

// --------------------------------------
// Autoload core classes and helpers
// --------------------------------------
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../app/Core/Helpers.php';

// --------------------------------------
// Bootstrap: runs global setup like tracking + asset prep
// --------------------------------------
require_once __DIR__ . '/../app/Core/Bootstrap.php'; // This now includes Tracker::trackVisit()

use App\Core\Config;
use App\Core\Template;
use App\Core\Route;
use App\Core\Tracker;

// --------------------------------------
// Load essential config files
// --------------------------------------
Config::load('site');
Config::load('meta'); // Meta titles/descriptions

// --------------------------------------
// Load route definitions (web + api)
// --------------------------------------
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';

// --------------------------------------
// Try matching a route (web or API)
// --------------------------------------
ob_start();
Route::dispatch();
$content = ob_get_clean();

if (!empty($content)) {
    echo $content;
    return;
}

// --------------------------------------
// Fallback: Try rendering a blade view file directly
// --------------------------------------
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'])));
$cleanUri = preg_replace('#^' . preg_quote($base) . '#', '', $uri);
$cleanUri = trim($cleanUri, '/');

// Default to 'home' if empty
$page = $cleanUri ?: 'home';

// Blade view fallback path
$viewPath = __DIR__ . "/../resources/views/pages/{$page}.blade.php";

if (file_exists($viewPath)) {
    Template::render($page, [
        'title' => config("meta.$page.title", config('site.default_title')),
        'description' => config("meta.$page.description", config('site.default_description'))
    ]);
} else {
    http_response_code(404);
    Template::render('errors/404', [
        'title' => '404 Not Found',
        'description' => 'The page you’re looking for doesn’t exist.'
    ]);
}
