<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust proxies - MUST be first for Ngrok/reverse proxy to work
        // WHY: Ngrok acts as a reverse proxy and adds X-Forwarded-* headers.
        // Laravel needs to trust these headers to detect HTTPS and correct host.
        $middleware->trustProxies(at: '*');

        // Trust Ngrok proxy - handles URL generation for Ngrok requests
        // WHY: Ngrok forwards HTTPS requests as HTTP internally. This middleware
        // detects Ngrok requests and forces proper HTTPS URL generation.
        $middleware->prepend(\App\Http\Middleware\TrustNgrokProxy::class);

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Exclude routes from CSRF verification
        // WHY: API routes use token-based auth (Sanctum tokens), not cookies.
        // Webhooks come from external services without CSRF tokens.
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
