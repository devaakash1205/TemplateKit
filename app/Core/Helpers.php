<?php

use App\Core\Config;
use App\Core\Assets;
use App\Core\ViewResponse;

/**
 * Get config value using dot notation (supports deep nesting).
 */
function config(string $key, $default = null)
{
    $segments = explode('.', $key);
    $file = array_shift($segments);

    $config = Config::getFull($file); // Get entire config file as array

    foreach ($segments as $segment) {
        if (is_array($config) && array_key_exists($segment, $config)) {
            $config = $config[$segment];
        } else {
            return $default;
        }
    }

    return $config;
}

/**
 * Generate asset URL (for CSS, JS, images).
 */
function asset(string $path): string
{
    return config('site.base_url') . '/resources/assets/' . ltrim($path, '/');
}

/**
 * Escape output to prevent XSS.
 */
function e($value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * JSON response with status code.
 */
function json(array $data, int $code = 200): string
{
    http_response_code($code);
    header('Content-Type: application/json; charset=UTF-8');
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

// Auto-load all helper files in app/Core/Helpers/
$helperFiles = glob(__DIR__ . '/Helpers/*.php');

foreach ($helperFiles as $file) {
    require_once $file;
}

/**
 * Check if current request is for an API route.
 */
function isApiRequest(): bool
{
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    return str_starts_with($uri, '/api');
}

// ----------------------------------------------------
// Asset class alias (used as Asset::load())
// ----------------------------------------------------
if (!class_exists('Asset')) {
    class Asset extends Assets
    {
        // Inherits static load() from Assets
    }
}

// ----------------------------------------------------
// Tracker class alias (used as TrackerAlias::trackVisit())
// ----------------------------------------------------
if (!class_exists('Tracker')) {
    class Tracker extends \App\Core\Tracker
    {
        // Inherits static methods from \App\Core\Tracker
    }
}

function url(string $path = ''): string
{
    return rtrim(config('site.base_url'), '/') . '/' . ltrim($path, '/');
}


function route(string $name): string
{
    return \App\Core\Route::getUrlByName($name) ?? '#';
}

function view(string $view, array $data = []): ViewResponse
{
    return new ViewResponse($view, $data);
}