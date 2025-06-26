<?php

namespace App\Core;

/**
 * CSRF Token Manager for Form Protection
 */
class Csrf
{
    /**
     * Generate or get the CSRF token.
     */
    public static function token(): string
    {
        if (!isset($_SESSION)) session_start();

        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }

    /**
     * Include hidden input in HTML forms
     */
    public static function input(): string
    {
        return '<input type="hidden" name="_token" value="' . self::token() . '">';
    }

    /**
     * Verify that the token from request matches session
     */
    public static function verify(): bool
    {
        if (!isset($_SESSION)) session_start();

        $token = $_POST['_token'] ?? '';

        return isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);
    }

    /**
     * Automatically reject invalid CSRF (optional use in middleware)
     */
    public static function verifyOrAbort()
    {
        if (!self::verify()) {
            http_response_code(419); // Laravel-like CSRF error code
            exit('CSRF token mismatch.');
        }
    }
}
