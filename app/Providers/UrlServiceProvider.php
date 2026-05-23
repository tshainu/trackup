<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class UrlServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Force URL generation to use the actual request host (works behind proxies/iframes)
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'https';
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
            URL::forceRootUrl("{$scheme}://{$host}");
            if ($scheme === 'https') {
                URL::forceScheme('https');
            }
        } elseif (isset($_SERVER['HTTP_HOST'])) {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            URL::forceRootUrl("{$scheme}://{$host}");
        }
    }
}
