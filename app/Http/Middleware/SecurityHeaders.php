<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevenir clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // HSTS (solo en producción)
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Content Security Policy (CSP) - Ajustado para desarrollo
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:5173; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
               "font-src 'self' https://fonts.gstatic.com; " .
               "img-src 'self' data: https:; " .
               "connect-src 'self' http://localhost:8000 http://localhost:5173; " .
               "frame-ancestors 'none';";

        // Para desarrollo, permitir más flexibilidad
        if (app()->environment('local')) {
            $csp = "default-src 'self' http://localhost:5173; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:5173; " .
                   "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com http://localhost:5173; " .
                   "font-src 'self' https://fonts.gstatic.com data:; " .
                   "img-src 'self' data: https: http://localhost:5173; " .
                   "connect-src 'self' http://localhost:8000 http://localhost:5173 ws://localhost:5173; " .
                   "frame-ancestors 'none';";
        }

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}