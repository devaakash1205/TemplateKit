<?php

// --------------------------------------
// PSR-4 Autoload Core Classes from /app
// --------------------------------------
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return; // Not in App namespace
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// --------------------------------------
// Global Helper Functions
// --------------------------------------
require_once __DIR__ . '/app/Core/Helpers.php';

// --------------------------------------
// CLI Utility Classes (if needed)
// --------------------------------------
$cliFiles = glob(__DIR__ . '/app/CLI/*.php');
foreach ($cliFiles as $file) {
    require_once $file;
}

// --------------------------------------
// Load Base Configs (site-wide settings)
// --------------------------------------
\App\Core\Config::load('site');
