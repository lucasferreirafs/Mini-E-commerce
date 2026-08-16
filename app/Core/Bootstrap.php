<?php

declare(strict_types=1);

namespace App\Core;

use Dotenv\Dotenv;

final class Bootstrap
{
    public static function initialize(): void
    {
        $projectRoot = dirname(__DIR__, 2);

        $dotenv = Dotenv::createImmutable($projectRoot);

        $dotenv->safeLoad();

        self::configureErrors();
        self::applySecurityHeaders();
    }

    private static function configureErrors(): void
    {
        error_reporting(E_ALL);

        $debug = Env::bool('APP_DEBUG', false);

        ini_set(
            'display_errors',
            $debug ? '1' : '0'
        );

        ini_set('log_errors', '1');
    }

    private static function applySecurityHeaders(): void
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: strict-origin-when-cross-origin');

        header(
            "Content-Security-Policy: " .
            "default-src 'self'; " .
            "img-src 'self' data:; " .
            "style-src 'self'; " .
            "script-src 'self'; " .
            "base-uri 'self'; " .
            "frame-ancestors 'none';"
        );
    }
}