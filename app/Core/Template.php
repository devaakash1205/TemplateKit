<?php

namespace App\Core;

use App\Core\Blade;

class Template
{
    public static function render(string $view, array $data = [])
    {
        $viewPath = str_replace('.', '/', $view);
        $contentView = __DIR__ . "/../../resources/views/pages/{$viewPath}.blade.php";

        if (!file_exists($contentView)) {
            http_response_code(404);
            $contentView = __DIR__ . "/../../resources/views/errors/404.blade.php";
        }

        extract($data);

        // Load and parse the content view
        $rawContent = file_get_contents($contentView);
        $parsedContent = Blade::parse(str_replace('@csrf', \App\Core\Csrf::input(), $rawContent));

        ob_start();
        eval('?>' . $parsedContent);
        $GLOBALS['compiledView'] = ob_get_clean(); // same key, but HTML content not file path

        // Check if layout is specified
        $layout = $GLOBALS['layout'] ?? null;

        if ($layout) {
            $layoutPath = __DIR__ . '/../../resources/views/' . str_replace('.', '/', $layout) . '.blade.php';

            if (file_exists($layoutPath)) {
                $rawLayout = file_get_contents($layoutPath);
                $parsedLayout = Blade::parse($rawLayout);

                ob_start();
                eval('?>' . $parsedLayout);
                echo ob_get_clean();
                return;
            }
        }

        // No layout: render view directly
        echo $GLOBALS['compiledView'];
    }
}
