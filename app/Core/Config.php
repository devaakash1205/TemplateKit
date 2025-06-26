<?php

namespace App\Core;

class Config
{
    protected static $config = [];

    /**
     * Load a config file (e.g. site.php).
     */
    public static function load(string $file)
    {
        $path = __DIR__ . "/../../config/{$file}.php";
        if (file_exists($path)) {
            self::$config[$file] = require $path;
        }
    }

    /**
     * Get a specific key from a config file.
     */
    public static function get(string $file, string $key, $default = null)
    {
        return self::$config[$file][$key] ?? $default;
    }

    /**
     * Get all keys from a config file.
     */
    public static function getFull(string $file): array
    {
        return self::$config[$file] ?? [];
    }
}
