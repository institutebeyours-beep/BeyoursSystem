<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\Check2FA;
use App\Http\Middleware\SessionTimeout;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\SessionControl;
use App\Http\Middleware\AnomalyDetection;
use App\Http\Middleware\MaintenanceMode;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            RateLimiter::for('api', function (Request $request) {
                return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
            });

            RateLimiter::for('login', function (Request $request) {
                return Limit::perMinute(5)->by($request->ip());
            });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ MIDDLEWARES GLOBALES
        // ⚠️ COMENTADO TEMPORALMENTE PARA SALIR DEL MODO MANTENIMIENTO
        // $middleware->prepend(MaintenanceMode::class);
        $middleware->append(SecurityHeaders::class);
        
        // Grupo web
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
        
        // ✅ Grupo API
        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\SessionTimeout::class,
        ]);
        
        // Alias de middlewares
        $middleware->alias([
            'throttle.login' => \Illuminate\Routing\Middleware\ThrottleRequests::class . ':5,1',
            'session.control' => SessionControl::class,
            'anomaly.detection' => AnomalyDetection::class,
            'session.timeout' => SessionTimeout::class,
            'verified' => EnsureEmailIsVerified::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'check.2fa' => Check2FA::class,
        ]);
        
        // Excepciones CSRF
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Manejo de errores de autenticación
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'No autenticado. Token inválido o expirado.',
                    'status' => 'error'
                ], 401);
            }
            return redirect()->guest('/login');
        });
        
        // Manejo de errores de autorización
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'No autorizado para realizar esta acción.',
                    'status' => 'error'
                ], 403);
            }
            return redirect()->back()->with('error', $e->getMessage());
        });
    })
    ->create();