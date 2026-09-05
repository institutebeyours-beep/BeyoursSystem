<?php

use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\EducationTypeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManualPdfController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// =============================================
// 1. RUTAS DEL MÓDULO ACADÉMICO
// =============================================
// ✅ Mejor: Usar Route::group en lugar de require
Route::prefix('academic')
    ->middleware(['auth:sanctum', 'role:academico|admin|super-admin'])
    ->group(base_path('routes/academic.php'));

// =============================================
// 2. CONFIGURACIONES GLOBALES (PÚBLICAS)
// =============================================
Route::get('/settings/public', [SettingsController::class, 'public']);
// ========================================== //
// 🏛️ TIPOS DE ENSEÑANZA (PÚBLICO)
// ========================================== //
Route::get('/education-types/public', [EducationTypeController::class, 'public'])
    ->middleware('auth:sanctum');
// =============================================
// 3. AUTENTICACIÓN (PÚBLICAS)
// =============================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// =============================================
// 4. RECUPERACIÓN DE CONTRASEÑA (CONDICIONAL)
// =============================================
Route::post('/password/forgot', function (Request $request) {
    if (!SettingsHelper::get('password_recovery', true)) {
        return response()->json([
            'message' => 'La recuperación de contraseña está deshabilitada',
        ], 404);
    }
    return app(PasswordResetController::class)->sendResetLink($request);
})->name('password.forgot');

Route::post('/password/reset', function (Request $request) {
    if (!SettingsHelper::get('password_recovery', true)) {
        return response()->json([
            'message' => 'La recuperación de contraseña está deshabilitada',
        ], 404);
    }
    return app(PasswordResetController::class)->resetPassword($request);
})->name('password.reset');

// =============================================
// 5. 2FA VERIFICACIÓN (PÚBLICA)
// =============================================
Route::post('/2fa/verify', [TwoFactorController::class, 'verifyLogin'])->name('2fa.verify');

// =============================================
// 6. RUTAS DE PERFIL Y USUARIO (AUTENTICADAS)
// =============================================
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/upload-image', [ProfileController::class, 'uploadImage'])->name('profile.upload-image');
    Route::delete('/profile/remove-image', [ProfileController::class, 'removeImage'])->name('profile.remove-image');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/user', [AuthController::class, 'user'])->name('user');
    
    Route::get('/2fa/generate', [TwoFactorController::class, 'generateQR'])->name('2fa.generate');
    Route::post('/2fa/enable', [TwoFactorController::class, 'verifyAndEnable'])->name('2fa.enable');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
    
    Route::post('/email/verification-notification', function (Request $request) {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email ya verificado'], 400);
        }
        $user->sendEmailVerificationNotification();
        return response()->json(['message' => 'Link de verificación enviado']);
    })->name('verification.send');
});

// =============================================
// 7. RUTAS DE ADMINISTRACIÓN
// =============================================
Route::middleware(['auth:sanctum', 'role:super-admin|admin', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
    
    // 7.1. Dashboard (solo super-admin)
    Route::middleware(['role:super-admin'])->group(function () {
        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    });
    
    // 7.2. Usuarios (CRUD)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('permission:view_users')->name('index');
        Route::get('/{id}', [UserController::class, 'show'])->middleware('permission:view_users')->name('show');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:create_users')->name('store');
        Route::put('/{id}', [UserController::class, 'update'])->middleware('permission:edit_users')->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->middleware('permission:delete_users')->name('destroy');
        Route::post('/{id}/password', [UserController::class, 'changePassword'])->middleware('permission:edit_users')->name('password');
    });
    
    // 7.3. Roles (CRUD)
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', function () {
            $roles = Spatie\Permission\Models\Role::with('permissions')->get();
            $permissions = Spatie\Permission\Models\Permission::all();
            foreach ($roles as $role) {
                $role->users_count = $role->users()->count();
            }
            return response()->json(['roles' => $roles, 'permissions' => $permissions]);
        })->middleware('permission:view_roles')->name('index');
        
        Route::post('/', function (Request $request) {
            $validated = $request->validate([
                'name' => 'required|string|unique:roles,name',
                'permissions' => 'array',
            ]);
            $role = Spatie\Permission\Models\Role::create([
                'name' => $validated['name'],
                'guard_name' => 'web'
            ]);
            if (isset($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            }
            return response()->json(['message' => 'Rol creado exitosamente', 'role' => $role], 201);
        })->middleware('permission:create_roles')->name('store');
        
        Route::put('/{id}', function (Request $request, $id) {
            $role = Spatie\Permission\Models\Role::findOrFail($id);
            $validated = $request->validate([
                'name' => 'required|string|unique:roles,name,' . $id,
                'permissions' => 'array',
            ]);
            $role->update(['name' => $validated['name']]);
            if (isset($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            }
            return response()->json(['message' => 'Rol actualizado exitosamente', 'role' => $role]);
        })->middleware('permission:edit_roles')->name('update');
        
        Route::delete('/{id}', function ($id) {
            $role = Spatie\Permission\Models\Role::findOrFail($id);
            if (in_array($role->name, ['super-admin', 'admin', 'manager', 'user'])) {
                return response()->json(['message' => 'No se puede eliminar un rol del sistema'], 403);
            }
            $role->delete();
            return response()->json(['message' => 'Rol eliminado exitosamente']);
        })->middleware('permission:delete_roles')->name('destroy');
    });
    
    // 7.4. Auditoría (solo super-admin)
    Route::middleware(['role:super-admin'])->prefix('audit')->name('audit.')->group(function () {
        Route::get('/', [AuditController::class, 'index'])->name('index');
        Route::get('/{id}', [AuditController::class, 'show'])->name('show');
        Route::delete('/{id}', [AuditController::class, 'destroy'])->name('destroy');
        Route::post('/clear', [AuditController::class, 'clear'])->name('clear');
    });
    
    // 7.5. Configuraciones
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/{key}', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/{key}/image', [SettingsController::class, 'updateImage'])->name('settings.image');
    Route::delete('/settings/{key}/image', [SettingsController::class, 'removeImage'])->name('settings.image-remove');
    
    // ========================================== //
    // ✅ 7.6. TIPOS DE ENSEÑANZA (NUEVO)
    // ========================================== //
    Route::prefix('education-types')->name('education-types.')->group(function () {
        Route::get('/', [EducationTypeController::class, 'index'])
            ->middleware('permission:education_types_view')->name('index');
        
        Route::get('/all', [EducationTypeController::class, 'all'])
            ->middleware('permission:education_types_view')->name('all');
        
        Route::post('/', [EducationTypeController::class, 'store'])
            ->middleware('permission:education_types_create')->name('store');
        
        Route::get('/{id}', [EducationTypeController::class, 'show'])
            ->middleware('permission:education_types_view')->name('show');
        
        Route::put('/{id}', [EducationTypeController::class, 'update'])
            ->middleware('permission:education_types_edit')->name('update');
        
        Route::delete('/{id}', [EducationTypeController::class, 'destroy'])
            ->middleware('permission:education_types_delete')->name('destroy');
    });
    
    // ========================================== //
    // 7.7. GESTIÓN DE PDFs DEL MANUAL
    // ========================================== //
    Route::prefix('manual-pdfs')->name('manual-pdfs.')->group(function () {
        Route::get('/', [ManualPdfController::class, 'index'])->name('index');
        Route::get('/role/{roleId}', [ManualPdfController::class, 'getByRole'])->name('role');
        Route::post('/upload', [ManualPdfController::class, 'upload'])->name('upload');
        Route::delete('/{id}', [ManualPdfController::class, 'destroy'])->name('destroy');
        Route::get('/roles', [ManualPdfController::class, 'getRoles'])->name('roles');
    });
});

// ========================================== //
// 8. RUTAS PÚBLICAS PARA MANUALES
// ========================================== //
Route::middleware(['auth:sanctum'])->prefix('manual')->name('manual.')->group(function () {
    Route::get('/role/{roleName}', [ManualPdfController::class, 'getByRoleName'])->name('role');
    Route::get('/download/{roleName}', [ManualPdfController::class, 'download'])->name('download');
    Route::get('/pdf/{roleId}', [ManualPdfController::class, 'getPublicPdfByRole'])->name('pdf');
});

// =============================================
// 9. MANTENIMIENTO (SOLO SUPER-ADMIN)
// =============================================
Route::middleware(['auth:sanctum', 'role:super-admin'])
    ->prefix('admin/maintenance')
    ->name('maintenance.')
    ->group(function () {
        Route::get('/status', [MaintenanceController::class, 'status'])->name('status');
        Route::post('/toggle', [MaintenanceController::class, 'toggleMaintenance'])->name('toggle');
        Route::post('/clear-cache', [MaintenanceController::class, 'clearCache'])->name('clear-cache');
        Route::get('/system-info', [MaintenanceController::class, 'systemInfo'])->name('system-info');
        Route::post('/clean-logs', [MaintenanceController::class, 'cleanLogs'])->name('clean-logs');
        
        Route::post('/backup', [MaintenanceController::class, 'createBackup'])->name('backup.create');
        Route::get('/backups', [MaintenanceController::class, 'listBackups'])->name('backup.list');
        Route::get('/backup/download/{filename}', [MaintenanceController::class, 'downloadBackup'])->name('backup.download');
        Route::delete('/backup/{filename}', [MaintenanceController::class, 'deleteBackup'])->name('backup.delete');
    });