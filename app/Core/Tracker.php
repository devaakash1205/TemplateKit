<?php

namespace App\Core;

class Tracker
{
    protected static bool $hasTracked = false;
    protected static ?string $cachedScripts = null;

    /**
     * Combined: track visit + return rendered scripts
     */
    public static function load(): string
    {
        self::trackVisit();
        return self::renderScripts();
    }

    /**
     * Log visit (once per request)
     */
    public static function trackVisit(): void
    {
        if (self::$hasTracked) return;

        self::$hasTracked = true;

        $logFile = __DIR__ . '/../../storage/traffic.log';

        $data = [
            'timestamp'     => date('Y-m-d H:i:s'),
            'ip'            => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            'url'           => $_SERVER['REQUEST_URI'] ?? 'N/A',
            'referrer'      => $_SERVER['HTTP_REFERER'] ?? 'Direct',
            'user_agent'    => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'utm_source'    => $_GET['utm_source'] ?? '',
            'utm_medium'    => $_GET['utm_medium'] ?? '',
            'utm_campaign'  => $_GET['utm_campaign'] ?? '',
        ];

        @file_put_contents($logFile, json_encode($data) . PHP_EOL, FILE_APPEND);
    }

    /**
     * Return HTML for tracking scripts (GA, Meta Pixel)
     */
    public static function renderScripts(): string
    {
        if (self::$cachedScripts !== null) return self::$cachedScripts;

        $scripts = '';

        // Google Analytics (GA4)
        if (config('seo.enable_google_analytics')) {
            $gaId = config('seo.google_analytics_id');
            $scripts .= <<<HTML
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$gaId}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{$gaId}');
</script>
HTML;
        }

        // Meta Pixel
        if (config('seo.enable_meta_pixel')) {
            $pixelId = config('seo.meta_pixel_id');
            $scripts .= <<<HTML
<!-- Meta Pixel -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window,document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '{$pixelId}');
  fbq('track', 'PageView');
</script>
<noscript>
  <img height="1" width="1" style="display:none"
       src="https://www.facebook.com/tr?id={$pixelId}&ev=PageView&noscript=1"/>
</noscript>
HTML;
        }

        return self::$cachedScripts = $scripts;
    }
}
