<?php

namespace App\Core;

class Request
{
    /**
     * Get a specific input value from GET or POST.
     */
    public static function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    /**
     * Get all sanitized input data.
     */
    public static function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * Check if a specific key exists.
     */
    public static function has(string $key): bool
    {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    /**
     * Check if the request is a POST request.
     */
    public static function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Sanitize a string input.
     */
    public static function clean(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get the current URI path.
     */
    public static function uri(): string
    {
        return trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    }

    /**
     * Get the HTTP method.
     */
    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }
}
