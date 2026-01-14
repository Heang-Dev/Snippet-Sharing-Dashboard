<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to handle Ngrok proxy for local team testing.
 *
 * This middleware:
 * 1. Detects when the request comes through Ngrok
 * 2. Properly sets the request scheme and host
 * 3. Forces HTTPS URL generation for Ngrok requests
 *
 * WHY: Ngrok provides HTTPS tunnels, but Laravel sees the local HTTP request.
 * This causes mixed content issues and incorrect URL generation.
 */
class TrustNgrokProxy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only process in local/development environment
        if (!app()->environment('local', 'development', 'testing')) {
            return $next($request);
        }

        // Check if request is coming through Ngrok
        $host = $request->getHost();
        $isNgrok = str_contains($host, 'ngrok-free.app') ||
                   str_contains($host, 'ngrok.io') ||
                   str_contains($host, 'ngrok');

        if ($isNgrok) {
            // Ngrok always uses HTTPS, so force URL scheme
            URL::forceScheme('https');

            // Set the root URL to match Ngrok
            URL::forceRootUrl('https://' . $host);

            // Trust the Ngrok proxy headers
            $request->server->set('HTTPS', 'on');
            $request->server->set('SERVER_PORT', 443);

            // Mark request as from Ngrok (useful for other middleware/services)
            $request->attributes->set('is_ngrok', true);
            $request->attributes->set('ngrok_host', $host);
        }

        return $next($request);
    }
}
