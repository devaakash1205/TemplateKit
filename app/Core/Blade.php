<?php

namespace App\Core;

class Blade
{
    /**
     * Render a Blade-style component from resources/views/components/
     * Supports dot notation: 'header.nav' → header/nav.blade.php
     */
    public static function component(string $name, array $data = []): void
    {
        $path = self::resolveComponentPath($name);

        if (!file_exists($path)) {
            echo "<!-- Component not found: $name -->";
            return;
        }

        extract($data);
        include $path;
    }

    /**
     * Resolve component path from dot notation or slash
     */
    protected static function resolveComponentPath(string $name): string
    {
        $relativePath = str_replace('.', '/', $name) . '.blade.php';
        return __DIR__ . '/../../resources/views/components/' . $relativePath;
    }

    /**
     * Preprocess raw Blade content:
     * - @component('header.nav')     → PHP component render
     * - $header.navbar$              → PHP component render
     * - $asset.path/to/file.css$     → URL resolved with base_url
     */
    public static function parse(string $content): string
    {
        // 1. Handle @layout('layouts.master') — sets global layout
        if (preg_match('/@layout\([\'"]([\w\.\/-]+)[\'"]\)/', $content, $match)) {
            $GLOBALS['layout'] = $match[1];
            $content = str_replace($match[0], '', $content); // remove @layout
        }

        // 2. Handle @component('header.navbar') → replace with actual file content + preserve indent
        $content = preg_replace_callback('/^[ \t]*@component\([\'"]([\w\.\/-]+)[\'"]\)/m', function ($matches) {
            $fullLine = $matches[0];
            $path = $matches[1];
            $indent = preg_match('/^([ \t]*)/', $fullLine, $i) ? $i[1] : '';

            $relative = str_replace('.', '/', strtolower($path));
            $filePath = __DIR__ . '/../../resources/views/components/' . $relative . '.blade.php';

            if (file_exists($filePath)) {
                $file = file_get_contents($filePath);
                $file = str_replace(["\r\n", "\r"], "\n", $file); // Normalize line endings
                $lines = explode("\n", $file);
                $indented = implode("\n", array_map(fn($line) => $indent . rtrim($line), $lines));
                return $indented;
            }

            return $indent . "<!-- Component not found: $path -->";
        }, $content);

        // 3. Handle $asset.path/to/file.css$
        $content = preg_replace_callback('/\$asset\.([a-zA-Z0-9_\-\/\.]+)\$/', function ($matches) {
            return config('site.base_url') . '/resources/assets/' . $matches[1];
        }, $content);

        // 4. Handle $header.navbar$ → render via Blade::component() (raw PHP for runtime eval)
        $content = preg_replace_callback('/\$([a-zA-Z0-9_\-\/\.]+)\$/', function ($matches) {
            return "<?php \\App\\Core\\Blade::component('{$matches[1]}'); ?>";
        }, $content);

        // 5. Handle @config('site.key') → inline PHP echo
        $content = preg_replace_callback('/@config\([\'"]([\w\.]+)[\'"]\)/', function ($matches) {
            $key = $matches[1];
            return "<?php echo config('$key'); ?>";
        }, $content);

        // 6. (Optional) Clean trailing whitespace and reduce multiple blank lines
        $content = preg_replace('/[ \t]+(\r?\n)/', '$1', $content);     // Remove trailing spaces
        $content = preg_replace("/\n{3,}/", "\n\n", $content);          // Collapse >2 line breaks

        return $content;
    }
}
