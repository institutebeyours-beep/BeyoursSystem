<?php

namespace App\Actions;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class LogUserAction
{
    /**
     * Registrar una acción del usuario
     */
    public static function log($userId, $action, $data = null)
    {
        $request = request();
        
        return AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'data' => $data ? json_encode($data) : null,
        ]);
    }

    /**
     * Registrar login exitoso
     */
    public static function loginSuccess($user)
    {
        return self::log($user->id, 'login_success', [
            'email' => $user->email,
            'method' => 'password'
        ]);
    }

    /**
     * Registrar login fallido
     */
    public static function loginFailed($email, $ip)
    {
        return AuditLog::create([
            'user_id' => null,
            'action' => 'login_failed',
            'ip_address' => $ip,
            'user_agent' => request()->userAgent(),
            'data' => json_encode(['email' => $email]),
        ]);
    }

    /**
     * Registrar logout
     */
    public static function logout($user)
    {
        return self::log($user->id, 'logout');
    }

    /**
     * Registrar cambio de contraseña
     */
    public static function passwordChanged($user)
    {
        return self::log($user->id, 'password_changed');
    }

    /**
     * Registrar cambio de email
     */
    public static function emailChanged($user, $oldEmail, $newEmail)
    {
        return self::log($user->id, 'email_changed', [
            'old' => $oldEmail,
            'new' => $newEmail
        ]);
    }

    /**
     * Registrar acción de administrador
     */
    public static function adminAction($user, $action, $target = null)
    {
        return self::log($user->id, "admin_$action", [
            'target' => $target,
            'timestamp' => now()
        ]);
    }
}