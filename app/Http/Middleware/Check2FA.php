<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;

class Check2FA
{
    public function handle(Request $request, Closure $next): Response
    {
        // Excluir rutas públicas
        $excludedRoutes = ['2fa/setup', '2fa/*', 'login', 'register', 'password/*', 'logout', 'api/*'];
        foreach ($excludedRoutes as $route) {
            if ($request->is($route)) {
                return $next($request);
            }
        }

        $user = auth()->user();
        
        if (!$user) {
            return $next($request);
        }
        
        $isAdmin = $user->hasRole('super-admin') || $user->hasRole('admin');
        $twoFactorRequired = Setting::getValue('2fa_required');
        
        // ✅ Si debe configurar 2FA, devolver un código de estado especial
        if ($twoFactorRequired && !$isAdmin && !$user->two_factor_secret) {
            // ✅ Si es una petición AJAX/JSON, devolver respuesta estructurada
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'requires_2fa_setup' => true,
                    'message' => 'Debes configurar 2FA antes de continuar',
                    'redirect' => '/2fa/setup'
                ], 403);
            }
            
            // ✅ Para peticiones web, redirigir (pero esto no se usa en SPA)
            return redirect('/2fa/setup');
        }
        
        return $next($request);
    }
}