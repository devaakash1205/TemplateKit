<?php

namespace App\Core;

class Controller
{
    protected function view(string $view, array $data = []): ViewResponse
    {
        return new ViewResponse($view, $data);
    }

    protected function redirect(string $url)
    {
        header("Location: {$url}");
        exit;
    }

    protected function json(array $data, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function validate(array $rules)
    {
        return Validator::make($_POST, $rules);
    }

    protected function input(string $key = null, $default = null)
    {
        if ($key === null) return $_POST;
        return $_POST[$key] ?? $default;
    }

    protected function csrf()
    {
        return Csrf::verify();
    }

    protected function flash(string $key, $value)
    {
        $_SESSION['flash'][$key] = $value;
    }

    protected function flashNow(string $key)
    {
        return $_SESSION['flash'][$key] ?? null;
    }
}
