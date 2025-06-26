<?php

namespace App\Core;

class Route
{
    protected static $routes = [];
    protected static $namedRoutes = [];

    public static function get(string $uri, callable $callback, string $name = null): void
    {
        self::register('GET', $uri, $callback, $name);
    }

    public static function post(string $uri, callable $callback, string $name = null): void
    {
        self::register('POST', $uri, $callback, $name);
    }

    protected static function register(string $method, string $uri, callable $callback, ?string $name): void
    {
        $uri = self::normalize($uri);
        self::$routes[$method][$uri] = $callback;

        $routeName = $name ?? self::uriToName($uri);
        self::$namedRoutes[$routeName] = $uri;
    }

    protected static function normalize(string $uri): string
    {
        return '/' . trim($uri, '/');
    }

    protected static function uriToName(string $uri): string
    {
        return trim(str_replace('/', '_', $uri), '_') ?: 'home';
    }

    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $base = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'])));
        $cleanedUri = preg_replace('#^' . preg_quote($base . '/api') . '#', '', $uri);
        $cleanedUri = preg_replace('#^' . preg_quote($base) . '#', '', $cleanedUri);
        $cleanedUri = self::normalize($cleanedUri);

        $callback = self::$routes[$method][$cleanedUri] ?? null;

        if (is_callable($callback)) {
            $response = call_user_func($callback);

            if ($response instanceof \App\Core\ViewResponse) {
                $response->render();
            } elseif (is_array($response)) {
                header('Content-Type: application/json');
                echo json_encode($response);
            } else {
                echo $response;
            }
        } else {
            http_response_code(404);
            self::handleNotFound();
        }
    }

    protected static function handleNotFound(): void
    {
        if (function_exists('isApiRequest') && isApiRequest()) {
            echo json_encode(['error' => 'Route not found']);
        } else {
            \App\Core\Template::render('errors/404', ['title' => 'Page Not Found']);
        }
    }

    public static function getUrlByName(string $name): ?string
    {
        $uri = self::$namedRoutes[$name] ?? null;
        if ($uri === null) return null;

        return rtrim(config('site.base_url'), '/') . $uri;
    }
}
