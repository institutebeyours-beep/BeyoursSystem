<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Events\Registered;
use App\Actions\LogUserAction;
use App\Helpers\SettingsHelper;

class AuthController extends Controller
{
    /**
     * Registrar un nuevo usuario
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'uuid' => Str::uuid(),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        // ✅ Asignar rol por defecto
        $user->assignRole('user');

        // ✅ VERIFICAR CONFIGURACIÓN DE EMAIL_VERIFICATION
        $emailVerification = SettingsHelper::get('email_verification', true);

        if ($emailVerification) {
            // Enviar email de verificación
            event(new Registered($user));
            
            return response()->json([
                'message' => 'Usuario registrado exitosamente. Por favor, verifica tu correo electrónico.',
                'requires_verification' => true,
                'user' => [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => $user->is_active,
                    'email_verified_at' => $user->email_verified_at,
                    'profile_image' => $user->profile_image,
                    'roles' => $user->getRoleNames()->toArray(),
                ],
            ], 201);
        }

        // ✅ Si NO requiere verificación, login automático
        $token = $user->createToken('auth_token')->plainTextToken;

        LogUserAction::loginSuccess($user);

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'requires_verification' => false,
            'user' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'lastname' => $user->lastname,
                'second_lastname' => $user->second_lastname,
                'email' => $user->email,
                'phone' => $user->phone,
                'cellphone' => $user->cellphone,
                'birth_date' => $user->birth_date,
                'address' => $user->address,
                'profile_image' => $user->profile_image,
                'is_active' => $user->is_active,
                'email_verified_at' => $user->email_verified_at,
                'last_login_at' => $user->last_login_at,
                'created_at' => $user->created_at,
                'roles' => $user->getRoleNames()->toArray(),
                'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            ],
            'token' => $token,
        ], 201);
    }

    /**
     * Iniciar sesión con todas las configuraciones de seguridad
     */
    public function login(Request $request)
    {
        // ========================================== //
        // 🔐 LEER CONFIGURACIONES DE SEGURIDAD       //
        // ========================================== //
        $maxAttempts = (int) SettingsHelper::get('max_login_attempts', 5);
        $blockTime = (int) SettingsHelper::get('login_block_time', 15);
        $twoFactorRequired = SettingsHelper::get('2fa_required', false);
        
        $key = 'login_attempts_' . $request->ip();
        $blockKey = 'login_blocked_' . $request->ip();
        
        \Log::info('🔐 Login - Configuraciones:', [
            'max_attempts' => $maxAttempts,
            'block_time' => $blockTime,
            '2fa_required' => $twoFactorRequired,
            'ip' => $request->ip(),
            'email' => $request->email,
        ]);

        // ========================================== //
        // 🔒 VERIFICAR SI ESTÁ BLOQUEADO             //
        // ========================================== //
        $blockedUntil = Cache::get($blockKey);
        if ($blockedUntil && now()->lessThan($blockedUntil)) {
            $remaining = ceil(now()->diffInMinutes($blockedUntil));
            \Log::warning('⛔ Intento de login bloqueado:', [
                'ip' => $request->ip(),
                'email' => $request->email,
                'remaining_minutes' => $remaining,
            ]);
            
            return response()->json([
                'message' => "Demasiados intentos. Bloqueado por {$remaining} minutos.",
                'blocked_until' => $blockedUntil->toISOString(),
                'remaining_minutes' => $remaining,
            ], 429);
        }

        // ========================================== //
        // ✅ VALIDAR CREDENCIALES                    //
        // ========================================== //
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Verificar credenciales y estado del usuario
        if (!$user || !$user->is_active || !Hash::check($request->password, $user->password)) {
            // Incrementar intentos fallidos
            RateLimiter::hit($key);
            $attempts = RateLimiter::attempts($key);
            $attemptsLeft = max(0, $maxAttempts - $attempts);
            
            \Log::warning('❌ Intento de login fallido:', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'attempts' => $attempts,
                'attempts_left' => $attemptsLeft,
            ]);

            // Verificar si se alcanzó el límite
            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                // Bloquear por X minutos
                Cache::put($blockKey, now()->addMinutes($blockTime), $blockTime * 60);
                RateLimiter::clear($key);
                
                LogUserAction::loginFailed($request->email, $request->ip());
                
                return response()->json([
                    'message' => "Demasiados intentos. Bloqueado por {$blockTime} minutos.",
                    'blocked_until' => now()->addMinutes($blockTime)->toISOString(),
                    'block_time' => $blockTime,
                ], 429);
            }
            
            LogUserAction::loginFailed($request->email, $request->ip());
            
            return response()->json([
                'message' => 'Credenciales incorrectas',
                'attempts_left' => $attemptsLeft,
            ], 401);
        }

        // ========================================== //
        // ✅ LOGIN EXITOSO - LIMPIAR INTENTOS        //
        // ========================================== //
        RateLimiter::clear($key);
        Cache::forget($blockKey);
        
        \Log::info('✅ Login exitoso:', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        // ✅ Obtener el primer rol del usuario
        $primaryRole = $user->roles()->first();

        // ========================================== //
        // 🔐 VERIFICAR 2FA                           //
        // ========================================== //
        if ($twoFactorRequired && $user->two_factor_secret && $user->two_factor_confirmed_at) {
            $tempToken = Str::random(60);
            $user->two_factor_temp_token = $tempToken;
            $user->save();

            \Log::info('🔐 2FA requerido:', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return response()->json([
                'message' => 'Se requiere código 2FA',
                'requires_2fa' => true,
                'temp_token' => $tempToken,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_image' => $user->profile_image,
                    'two_factor_secret' => $user->two_factor_secret,
                    'two_factor_confirmed_at' => $user->two_factor_confirmed_at,
                    'role_id' => $primaryRole?->id,
                    'role_name' => $primaryRole?->name,
                    'roles' => $user->getRoleNames()->toArray(),
                ]
            ]);
        }

        // ========================================== //
        // 🔥 FORZAR CONFIGURACIÓN 2FA (si aplica)    //
        // ========================================== //
        $isAdmin = $user->hasRole('super-admin') || $user->hasRole('admin');
        $has2FA = !empty($user->two_factor_secret);

        $condicion2FA = ($twoFactorRequired == true || $twoFactorRequired === '1' || $twoFactorRequired === 1) 
                        && !$isAdmin 
                        && empty($user->two_factor_secret);

        if ($condicion2FA) {
            \Log::info('⚠️ Forzando configuración 2FA para:', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            
            $token = $user->createToken('auth_token')->plainTextToken;
            
            LogUserAction::loginSuccess($user);

            return response()->json([
                'message' => 'Debes configurar 2FA',
                'requires_2fa_setup' => true,
                'user' => [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'lastname' => $user->lastname,
                    'second_lastname' => $user->second_lastname,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'cellphone' => $user->cellphone,
                    'birth_date' => $user->birth_date,
                    'address' => $user->address,
                    'profile_image' => $user->profile_image,
                    'is_active' => $user->is_active,
                    'last_login_at' => $user->last_login_at,
                    'created_at' => $user->created_at,
                    'role_id' => $primaryRole?->id,
                    'role_name' => $primaryRole?->name,
                    'roles' => $user->getRoleNames()->toArray(),
                    'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                ],
                'token' => $token,
            ]);
        }

        // ========================================== //
        // ✅ LOGIN EXITOSO COMPLETO                  //
        // ========================================== //
        $user->updateLastLogin();
        $token = $user->createToken('auth_token')->plainTextToken;

        LogUserAction::loginSuccess($user);

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'success' => true,
            'user' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'lastname' => $user->lastname,
                'second_lastname' => $user->second_lastname,
                'email' => $user->email,
                'phone' => $user->phone,
                'cellphone' => $user->cellphone,
                'birth_date' => $user->birth_date,
                'address' => $user->address,
                'profile_image' => $user->profile_image,
                'is_active' => $user->is_active,
                'last_login_at' => $user->last_login_at,
                'created_at' => $user->created_at,
                'email_verified_at' => $user->email_verified_at,
                'two_factor_secret' => $user->two_factor_secret,
                'two_factor_confirmed_at' => $user->two_factor_confirmed_at,
                'role_id' => $primaryRole?->id,
                'role_name' => $primaryRole?->name,
                'roles' => $user->getRoleNames()->toArray(),
                'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            ],
            'token' => $token,
        ]);
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        
        // Limpiar cache de actividad de sesión
        $sessionId = session()->getId();
        Cache::forget("user_last_activity_{$user->id}_{$sessionId}");
        
        // Eliminar token actual
        $request->user()->currentAccessToken()->delete();

        \Log::info('🚪 Cierre de sesión:', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return response()->json([
            'message' => 'Sesión cerrada exitosamente'
        ]);
    }

    /**
     * Obtener el usuario autenticado
     */
    public function user(Request $request)
    {
        $user = $request->user();
        $primaryRole = $user->roles()->first();
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'lastname' => $user->lastname,
                'second_lastname' => $user->second_lastname,
                'email' => $user->email,
                'phone' => $user->phone,
                'cellphone' => $user->cellphone,
                'birth_date' => $user->birth_date,
                'address' => $user->address,
                'profile_image' => $user->profile_image,
                'is_active' => $user->is_active,
                'email_verified_at' => $user->email_verified_at,
                'last_login_at' => $user->last_login_at,
                'created_at' => $user->created_at,
                'role_id' => $primaryRole?->id,
                'role_name' => $primaryRole?->name,
                'roles' => $user->getRoleNames()->toArray(),
                'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            ]
        ]);
    }

    /**
     * Actualizar perfil del usuario
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Perfil actualizado exitosamente',
            'user' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => $user->is_active,
                'profile_image' => $user->profile_image,
                'roles' => $user->getRoleNames()->toArray(),
            ],
        ]);
    }
}