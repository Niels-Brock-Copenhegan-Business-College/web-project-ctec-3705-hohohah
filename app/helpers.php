<?php
declare(strict_types=1);

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = defined('APP_BASE') ? APP_BASE : '';
        return $base . '/' . ltrim($path, '/');
    }
}
