<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SessionControl
{
    /**
     * Número máximo de sesiones permitidas por usuario
     */
    protected $maxSessions = 3;
    
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $sessionId = session()->getId();
            
            // Obtener sesiones activas del usuario
            $userSessions = Cache::get("user_sessions_{$user->id}", []);
            
            // Si el usuario tiene sesiones activas
            if (!empty($userSessions)) {
                // Verificar si esta sesión ya está registrada
                if (!in_array($sessionId, $userSessions)) {
                    // Si excede el límite, cerrar la sesión más antigua
                    if (count($userSessions) >= $this->maxSessions) {
                        // Eliminar la sesión más antigua
                        $oldestSession = array_shift($userSessions);
                        
                        // Invalidar la sesión más antigua
                        // (Esto se puede hacer con un mecanismo de invalidez)
                    }
                    
                    // Agregar la nueva sesión
                    $userSessions[] = $sessionId;
                    Cache::put("user_sessions_{$user->id}", $userSessions, now()->addHours(24));
                }
            } else {
                // Primera sesión del usuario
                Cache::put("user_sessions_{$user->id}", [$sessionId], now()->addHours(24));
            }
        }
        
        return $next($request);
    }
}