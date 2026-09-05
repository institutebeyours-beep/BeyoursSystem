<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AnomalyDetection
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if ($user) {
            $ip = $request->ip();
            $userAgent = $request->userAgent();
            $lastLoginIp = Cache::get("user_last_ip_{$user->id}");
            
            // Detectar cambio de ubicación (IP)
            if ($lastLoginIp && $lastLoginIp !== $ip) {
                Log::warning("Cambio de ubicación detectado", [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'previous_ip' => $lastLoginIp,
                    'current_ip' => $ip,
                    'user_agent' => $userAgent
                ]);
                
                // Enviar alerta por email
                // $this->sendAlertEmail($user, $lastLoginIp, $ip);
            }
            
            // Guardar la IP actual
            Cache::put("user_last_ip_{$user->id}", $ip, now()->addDays(7));
        }
        
        return $next($request);
    }
}