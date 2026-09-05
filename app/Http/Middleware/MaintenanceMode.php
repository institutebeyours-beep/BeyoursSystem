<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\SettingsHelper;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next)
{
    // 1. Verificar si el modo mantenimiento está activo
    $maintenanceMode = SettingsHelper::get('maintenance_mode', false);
    
    // 2. PERMITIR ACCESO A SUPER-ADMIN (SIEMPRE)
    if ($request->user() && $request->user()->hasRole('super-admin')) {
        return $next($request);
    }

    // 3. Verificar IPs Bloqueadas (siempre aplica)
    $blockedIps = SettingsHelper::get('maintenance_block_ips', '');
    $blockedIpsArray = array_map('trim', explode(',', $blockedIps));
    $blockedIpsArray = array_filter($blockedIpsArray);
    
    if (in_array($request->ip(), $blockedIpsArray)) {
        \Log::warning('⛔ IP bloqueada:', [
            'ip' => $request->ip(),
            'user' => $request->user()?->email ?? 'Invitado'
        ]);
        
        return response()->json([
            'message' => 'Acceso denegado. IP bloqueada.',
            'blocked' => true,
        ], 403);
    }

    // 4. Si NO está en mantenimiento, permitir acceso
    if (!$maintenanceMode) {
        return $next($request);
    }

    // 5. Verificar IPs permitidas (Whitelist)
    $allowedIps = SettingsHelper::get('maintenance_allow_ips', '');
    $allowedIpsArray = array_map('trim', explode(',', $allowedIps));
    $allowedIpsArray = array_filter($allowedIpsArray);
    
    // Si hay IPs permitidas configuradas, verificar
    if (!empty($allowedIpsArray)) {
        if (in_array($request->ip(), $allowedIpsArray)) {
            return $next($request);
        }
        
        \Log::warning('⛔ IP no permitida en mantenimiento:', [
            'ip' => $request->ip(),
            'allowed_ips' => $allowedIpsArray,
            'user' => $request->user()?->email ?? 'Invitado'
        ]);
    }

    // 6. Si es API, devolver JSON
    if ($request->expectsJson() || $request->is('api/*')) {
        return response()->json([
            'message' => SettingsHelper::get('maintenance_message', 'Sistema en mantenimiento. Por favor, vuelve más tarde.'),
            'maintenance' => true,
            'status' => '503'
        ], 503);
    }

    // 7. Para web, mostrar vista de mantenimiento
    return response()->view('maintenance', [
        'message' => SettingsHelper::get('maintenance_message', 'Sistema en mantenimiento. Por favor, vuelve más tarde.'),
    ], 503);
}
}