<?php

namespace App\Core;

/**
 * Asset loader class for CSS/JS via assets.json
 */
class Assets
{
    protected static string $assetJsonPath = __DIR__ . '/../../resources/assets/assets.json';
    protected static string $assetBaseUrl;

    /**
     * Load all assets and return HTML tags (cached)
     */
    public static function load(): array
    {
        // Self-caching: loads once per request
        static $cachedAssets = null;
        if ($cachedAssets !== null) {
            return $cachedAssets;
        }

        self::$assetBaseUrl = config('site.base_url') . '/resources/assets';

        if (!file_exists(self::$assetJsonPath)) {
            return $cachedAssets = ['css' => '', 'js' => ''];
        }

        $assets = json_decode(file_get_contents(self::$assetJsonPath), true);

        $cssTags = '';
        $jsTags = '';

        foreach ($assets['css'] ?? [] as $css) {
            $cssTags .= '<link rel="stylesheet" href="' . self::getAssetPath('css', $css) . '">' . PHP_EOL;
        }

        foreach ($assets['js'] ?? [] as $js) {
            $jsTags .= '<script src="' . self::getAssetPath('js', $js) . '"></script>' . PHP_EOL;
        }

        return $cachedAssets = ['css' => $cssTags, 'js' => $jsTags];
    }

    /**
     * Shortcut for just CSS tags
     */
    public static function css(): string
    {
        return self::load()['css'];
    }

    /**
     * Shortcut for just JS tags
     */
    public static function js(): string
    {
        return self::load()['js'];
    }

    /**
     * Resolve asset path from local or CDN
     */
    protected static function getAssetPath(string $type, string $name): string
    {
        [$base, $version] = explode('@', $name) + [$name, null];

        $ext = $type === 'css' ? '.min.css' : '.min.js';
        $localPath = __DIR__ . "/../../resources/assets/$type/$base$ext";
        $localUrl  = self::$assetBaseUrl . "/$type/$base$ext";

        // Use local file if exists
        if (file_exists($localPath)) {
            return $localUrl;
        }

        // CDN fallback map
        $cdnMap = [
            'bootstrap' => [
                'css' => "https://cdn.jsdelivr.net/npm/bootstrap@$version/dist/css/bootstrap.min.css",
                'js'  => "https://cdn.jsdelivr.net/npm/bootstrap@$version/dist/js/bootstrap.bundle.min.js",
            ],
            'tailwind' => [
                'css' => "https://cdn.jsdelivr.net/npm/tailwindcss@$version/dist/tailwind.min.css",
            ],
            'jquery' => [
                'js' => "https://cdn.jsdelivr.net/npm/jquery@$version/dist/jquery.min.js",
            ],
            'alpine' => [
                'js' => "https://cdn.jsdelivr.net/npm/alpinejs@$version/dist/cdn.min.js",
            ],
            'fontawesome' => [
                'css' => "https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@$version/css/all.min.css",
            ],
            'animate' => [
                'css' => "https://cdn.jsdelivr.net/npm/animate.css@$version/animate.min.css",
            ],
        ];

        if (isset($cdnMap[$base][$type]) && $version) {
            return $cdnMap[$base][$type];
        }

        // Default to local URL
        return $localUrl;
    }
}
