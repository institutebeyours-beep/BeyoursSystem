<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use PragmaRX\Google2FAQRCode\Google2FA;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Generar QR para 2FA
     */
    public function generateQR(Request $request)
    {
        $user = $request->user();

        // Si ya tiene 2FA, no generar nuevo
        if ($user->two_factor_secret) {
            return response()->json([
                'message' => '2FA ya está habilitado'
            ], 400);
        }

        // Generar secreto
        $secret = $this->google2fa->generateSecretKey();

        // Guardar temporalmente (hasta que verifiquen)
        $user->two_factor_secret = $secret;
        $user->save();

        // Generar QR Code
        $qrCode = $this->google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret
        );

        // Generar códigos de respaldo
        $recoveryCodes = $this->generateRecoveryCodes($user);

        return response()->json([
            'secret' => $secret,
            'qr_code' => $qrCode,
            'recovery_codes' => $recoveryCodes
        ]);
    }

    /**
     * Verificar y activar 2FA
     */
    public function verifyAndEnable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = $request->user();

        if (!$user->two_factor_secret) {
            return response()->json([
                'message' => '2FA no está iniciado'
            ], 400);
        }

        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (!$valid) {
            return response()->json([
                'message' => 'Código inválido'
            ], 401);
        }

        // Activar 2FA (guardar confirmación)
        $user->two_factor_confirmed_at = now();
        $user->save();

        return response()->json([
            'message' => '2FA habilitado exitosamente',
            'recovery_codes' => json_decode($user->two_factor_recovery_codes)
        ]);
    }

    /**
     * ⚠️ Deshabilitar 2FA (SOLO ADMIN Y SUPER-ADMIN)
     */
    public function disable(Request $request)
    {
        $user = $request->user();

        // 🔥 VERIFICAR ROL: Solo admin o super-admin pueden desactivar
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            return response()->json([
                'message' => 'No tienes permisos para desactivar 2FA. Solo administradores pueden hacerlo.'
            ], 403);
        }

        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return response()->json([
            'message' => '2FA deshabilitado exitosamente'
        ]);
    }

    /**
     * Verificar 2FA durante login
     */
    public function verifyLogin(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'temp_token' => 'required|string'
        ]);

        // Buscar usuario por token temporal
        $user = User::where('two_factor_temp_token', $request->temp_token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Token inválido'
            ], 401);
        }

        if (!$user->two_factor_secret) {
            return response()->json([
                'message' => '2FA no está habilitado'
            ], 400);
        }

        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (!$valid) {
            return response()->json([
                'message' => 'Código 2FA inválido'
            ], 401);
        }

        // Limpiar token temporal
        $user->two_factor_temp_token = null;
        $user->save();

        // Generar token final
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'user' => $user->only(['id', 'name', 'email', 'roles']),
            'token' => $token
        ]);
    }

    /**
     * Generar códigos de respaldo
     */
    private function generateRecoveryCodes($user)
    {
        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $codes[] = $this->generateRecoveryCode();
        }

        $user->two_factor_recovery_codes = json_encode($codes);
        $user->save();

        return $codes;
    }

    private function generateRecoveryCode()
    {
        return strtoupper(substr(md5(uniqid()), 0, 8)) . '-' .
               strtoupper(substr(md5(uniqid()), 0, 6));
    }
}