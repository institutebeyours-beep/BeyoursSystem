<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\SettingsHelper;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        // Verificar si está habilitada la verificación
        if (!SettingsHelper::get('email_verification', true)) {
            return $next($request);
        }
        
        // Si no hay usuario autenticado
        if (!$user) {
            return response()->json([
                'message' => 'No autenticado.',
            ], 401);
        }
        
        // Verificar si el email está verificado
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Tu correo electrónico no está verificado.',
                'requires_verification' => true,
                'resend_url' => '/api/email/verification-notification',
            ], 403);
        }
        
        return $next($request);
    }
}