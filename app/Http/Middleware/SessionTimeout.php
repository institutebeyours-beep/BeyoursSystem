<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Helpers\SettingsHelper;

class SessionTimeout
{
    public function handle(Request $request, Closure $next)
    {
        // Solo verificar si hay usuario autenticado
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $sessionId = session()->getId();
        
        // Obtener tiempo de expiración de settings
        $timeout = (int) SettingsHelper::get('session_timeout', 120); // minutos
        
        // Si timeout es 0 o menor, no aplicar
        if ($timeout <= 0) {
            return $next($request);
        }
        
        $cacheKey = "user_last_activity_{$user->id}_{$sessionId}";
        $lastActivity = Cache::get($cacheKey);

        if ($lastActivity) {
            $inactiveTime = now()->diffInMinutes($lastActivity);
            
            // Si ha estado inactivo más del tiempo permitido
            if ($inactiveTime >= $timeout) {
                \Log::info('⏰ Sesión expirada por inactividad: ' . $user->email . ' (' . $inactiveTime . ' minutos)');
                
                // Cerrar sesión
                $user->currentAccessToken()?->delete();
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                
                return response()->json([
                    'message' => 'Su sesión ha expirado por inactividad',
                    'expired' => true,
                    'inactive_minutes' => $inactiveTime,
                    'timeout' => $timeout,
                ], 401);
            }
        }

        // Actualizar última actividad
        Cache::put($cacheKey, now(), now()->addMinutes($timeout + 5));

        return $next($request);
    }
}