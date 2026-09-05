<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use ZipArchive;
use App\Models\Backup;
use Spatie\Activitylog\Facades\Activity;
use App\Models\User;
use App\Notifications\MaintenanceStatusChanged;
use App\Notifications\BackupCreated;
use App\Notifications\BackupDeleted;
use App\Notifications\SystemAlert;

class MaintenanceController extends Controller
{
    /**
     * Verificar que el usuario es Super-Admin
     */
    private function checkSuperAdmin()
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'Solo el Super-Admin puede realizar esta acción');
        }
    }

    /**
     * Registrar actividad en auditoría
     */
    private function logActivity($description, $properties = [])
    {
        try {
            Activity::causedBy(auth()->user())
                ->withProperties($properties)
                ->log($description);
                
            \Log::info('📝 Auditoría registrada:', [
                'description' => $description,
                'user' => auth()->user()?->email,
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            \Log::error('❌ Error en auditoría:', [
                'message' => $e->getMessage(),
                'description' => $description,
            ]);
            return false;
        }
    }

    /**
     * Obtener estado del mantenimiento
     */
    public function status()
    {
        $this->checkSuperAdmin();
        
        return response()->json([
            'maintenance_mode' => (bool) SettingsHelper::get('maintenance_mode', false),
            'maintenance_message' => SettingsHelper::get('maintenance_message', ''),
            'maintenance_allow_ips' => SettingsHelper::get('maintenance_allow_ips', ''),
            'maintenance_block_ips' => SettingsHelper::get('maintenance_block_ips', ''),
            'disk_usage' => $this->getDiskUsage(),
            'log_retention_days' => (int) SettingsHelper::get('log_retention_days', 30),
            'auto_backup' => (bool) SettingsHelper::get('auto_backup', false),
            'backup_frequency' => SettingsHelper::get('backup_frequency', 'daily'),
            'backup_keep_days' => (int) SettingsHelper::get('backup_keep_days', 30),
            'auto_clean_logs' => (bool) SettingsHelper::get('auto_clean_logs', true),
        ]);
    }

    /**
     * Activar/Desactivar modo mantenimiento
     */
    public function toggleMaintenance(Request $request)
    {
        $this->checkSuperAdmin();
        
        $request->validate([
            'enabled' => 'required|boolean',
            'message' => 'nullable|string|max:500',
            'allow_ips' => 'nullable|string',
            'block_ips' => 'nullable|string',
        ]);

        $oldStatus = SettingsHelper::get('maintenance_mode', false);
        
        SettingsHelper::set('maintenance_mode', $request->enabled);
        
        if ($request->has('message')) {
            SettingsHelper::set('maintenance_message', $request->message);
        }
        
        if ($request->has('allow_ips')) {
            SettingsHelper::set('maintenance_allow_ips', $request->allow_ips);
        }
        
        if ($request->has('block_ips')) {
            SettingsHelper::set('maintenance_block_ips', $request->block_ips);
        }

        // ✅ REGISTRAR EN AUDITORÍA
        $this->logActivity(
            $request->enabled ? 'activó modo mantenimiento' : 'desactivó modo mantenimiento',
            [
                'module' => 'maintenance',
                'action' => $request->enabled ? 'activar' : 'desactivar',
                'old_status' => $oldStatus,
                'new_status' => $request->enabled,
                'message' => $request->message,
                'allow_ips' => $request->allow_ips,
                'block_ips' => $request->block_ips,
            ]
        );

        // ✅ NOTIFICAR AL SUPER-ADMIN
        $superAdmin = User::role('super-admin')->first();
        if ($superAdmin && $superAdmin->id !== auth()->id()) {
            $superAdmin->notify(new MaintenanceStatusChanged(
                $request->enabled,
                auth()->user()
            ));
        }

        if ($request->enabled) {
            Artisan::call('cache:clear');
        }

        return response()->json([
            'message' => $request->enabled 
                ? '🔒 Modo mantenimiento activado' 
                : '🔓 Modo mantenimiento desactivado',
            'status' => $request->enabled,
        ]);
    }

    /**
     * Limpiar caché del sistema
     */
    public function clearCache()
    {
        $this->checkSuperAdmin();
        
        // ✅ REGISTRAR EN AUDITORÍA
        $this->logActivity(
            'limpió la caché del sistema',
            [
                'module' => 'maintenance',
                'action' => 'limpiar_caché',
            ]
        );
        
        // ✅ NOTIFICAR AL SUPER-ADMIN
        $superAdmin = User::role('super-admin')->first();
        if ($superAdmin && $superAdmin->id !== auth()->id()) {
            $superAdmin->notify(new SystemAlert(
                "El Super-Admin " . auth()->user()->name . " ha limpiado la caché del sistema.",
                'info'
            ));
        }
        
        \Log::info('🧹 Super-Admin ' . auth()->user()->email . ' ha limpiado la caché');
        
        Artisan::call('optimize:clear');
        
        return response()->json([
            'message' => '✅ Caché limpiada correctamente',
            'output' => Artisan::output(),
        ]);
    }

    /**
     * Obtener información del sistema
     */
    public function systemInfo()
    {
        $this->checkSuperAdmin();
        
        return response()->json([
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'memory_usage' => $this->formatSize(memory_get_usage()),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'disk_free_space' => $this->formatSize(disk_free_space(base_path())),
            'disk_total_space' => $this->formatSize(disk_total_space(base_path())),
            'database_driver' => config('database.default'),
            'database_name' => config('database.connections.mysql.database'),
            'app_env' => app()->environment(),
            'app_debug' => config('app.debug'),
        ]);
    }

    /**
     * Limpiar logs antiguos
     */
    public function cleanLogs(Request $request)
    {
        $this->checkSuperAdmin();
        
        // ✅ REGISTRAR EN AUDITORÍA
        $this->logActivity(
            'limpió los logs del sistema',
            [
                'module' => 'maintenance',
                'action' => 'limpiar_logs',
            ]
        );
        
        // ✅ NOTIFICAR AL SUPER-ADMIN
        $superAdmin = User::role('super-admin')->first();
        if ($superAdmin && $superAdmin->id !== auth()->id()) {
            $superAdmin->notify(new SystemAlert(
                "El Super-Admin " . auth()->user()->name . " ha limpiado los logs del sistema.",
                'info'
            ));
        }
        
        $logPath = storage_path('logs');
        $deleted = 0;
        $files = [];
        $errors = [];
        
        if (!File::exists($logPath)) {
            return response()->json([
                'message' => 'La carpeta de logs no existe',
                'deleted' => 0,
            ]);
        }
        
        $logFiles = File::files($logPath);
        
        foreach ($logFiles as $file) {
            $filename = $file->getFilename();
            
            if ($filename === 'laravel.log') {
                continue;
            }
            
            try {
                $files[] = $filename;
                File::delete($file->getPathname());
                $deleted++;
            } catch (\Exception $e) {
                $errors[] = $filename . ': ' . $e->getMessage();
            }
        }
        
        $laravelLogPath = $logPath . DIRECTORY_SEPARATOR . 'laravel.log';
        if (File::exists($laravelLogPath)) {
            $content = "[" . now() . "] local.INFO: Logs limpiados manualmente por " . auth()->user()->email . "\n";
            File::put($laravelLogPath, $content);
        }
        
        \Log::info('🧹 Super-Admin ' . auth()->user()->email . ' ha limpiado los logs manualmente', [
            'deleted' => $deleted,
            'files' => $files,
            'errors' => $errors,
        ]);
        
        return response()->json([
            'message' => "✅ Se eliminaron {$deleted} archivos de log",
            'deleted' => $deleted,
            'files' => $files,
            'errors' => $errors,
        ]);
    }

    // ========================================== //
    // BACKUPS                                   //
    // ========================================== //

    /**
     * Listar backups disponibles
     */
    public function listBackups()
    {
        try {
            $this->checkSuperAdmin();
            
            $backups = Backup::orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'backups' => $backups,
                'total' => $backups->count(),
                'disk_usage' => $this->getBackupDiskUsage(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Error listando backups:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al listar backups: ' . $e->getMessage(),
                'backups' => [],
                'total' => 0,
                'disk_usage' => [
                    'total_size' => '0 B',
                    'total_size_bytes' => 0,
                    'file_count' => 0,
                ],
            ], 500);
        }
    }

    /**
     * Crear backup manual
     */
    public function createBackup(Request $request)
    {
        try {
            $this->checkSuperAdmin();
            
            $type = $request->input('type', 'full');
            
            $backupPath = storage_path('app/backups');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }
            
            if (!is_writable($backupPath)) {
                throw new \Exception('El directorio de backups no tiene permisos de escritura');
            }

            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $fileName = "backup_{$type}_{$timestamp}.zip";
            $filePath = $backupPath . '/' . $fileName;

            $zip = new ZipArchive();
            $result = $zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            
            if ($result !== true) {
                throw new \Exception('No se pudo crear el archivo ZIP. Código: ' . $result);
            }

            $sqlContent = $this->getDatabaseDump();
            $zip->addFromString('database.sql', $sqlContent);

            if ($type === 'full') {
                $this->addDirectoryToZip($zip, base_path(), 'app', [
                    'vendor', 'node_modules', 'storage', 'bootstrap/cache'
                ]);
            }

            $zip->close();

            if (!File::exists($filePath) || filesize($filePath) === 0) {
                throw new \Exception('El archivo ZIP no se creó correctamente');
            }

            // ✅ GUARDAR EN BD
            $backup = Backup::create([
                'filename' => $fileName,
                'type' => $type,
                'size' => $this->formatSize(filesize($filePath)),
                'size_bytes' => filesize($filePath),
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]);

            // ✅ REGISTRAR EN AUDITORÍA
            $this->logActivity(
                'creó un backup',
                [
                    'module' => 'backup',
                    'action' => 'crear',
                    'filename' => $fileName,
                    'type' => $type,
                    'size' => $this->formatSize(filesize($filePath)),
                ]
            );

            // ✅ NOTIFICAR AL SUPER-ADMIN
            $superAdmin = User::role('super-admin')->first();
            if ($superAdmin) {
                $superAdmin->notify(new BackupCreated($backup, auth()->user()));
            }

            \Log::info('💾 Backup creado exitosamente', [
                'file' => $fileName,
                'size' => filesize($filePath),
                'backup_id' => $backup->id,
                'user' => auth()->user()->email
            ]);

            return response()->json([
                'message' => 'Backup creado exitosamente',
                'backup' => $backup,
                'file' => $fileName,
                'size' => $this->formatSize(filesize($filePath)),
                'type' => $type,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error creando backup: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al crear backup: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener dump de la base de datos
     */
    private function getDatabaseDump()
    {
        try {
            $tables = \DB::select('SHOW TABLES');
            $database = config('database.connections.mysql.database');
            $content = "-- Backup generado por Laravel\n-- Fecha: " . now() . "\n-- Base de datos: {$database}\n\n";
            $content .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
            
            foreach ($tables as $table) {
                $tableName = current((array)$table);
                $content .= "-- Tabla: {$tableName}\n";
                
                try {
                    $createTable = \DB::select("SHOW CREATE TABLE {$tableName}");
                    if (!empty($createTable)) {
                        $createTableArray = (array)$createTable[0];
                        $content .= $createTableArray['Create Table'] . ";\n\n";
                    }
                } catch (\Exception $e) {
                    $content .= "-- Error obteniendo estructura de {$tableName}\n";
                }
                
                try {
                    $count = \DB::table($tableName)->count();
                    if ($count > 0) {
                        $rows = \DB::table($tableName)->get();
                        foreach ($rows as $row) {
                            $values = array_map(function($value) {
                                if (is_null($value)) return 'NULL';
                                if (is_numeric($value)) return $value;
                                return "'" . addslashes($value) . "'";
                            }, (array)$row);
                            $content .= "INSERT INTO {$tableName} VALUES (" . implode(', ', $values) . ");\n";
                        }
                        $content .= "\n";
                    }
                } catch (\Exception $e) {
                    $content .= "-- Error exportando datos de {$tableName}\n";
                }
            }
            
            $content .= "SET FOREIGN_KEY_CHECKS=1;\n";
            return $content;
            
        } catch (\Exception $e) {
            \Log::error('Error generando dump de BD: ' . $e->getMessage());
            return "-- Error generando backup: " . $e->getMessage();
        }
    }

    /**
     * Descargar backup
     */
    public function downloadBackup($filename)
    {
        try {
            $this->checkSuperAdmin();
            
            $path = storage_path('app/backups/' . $filename);
            
            if (!File::exists($path)) {
                return response()->json(['message' => 'Archivo no encontrado'], 404);
            }

            // ✅ REGISTRAR EN AUDITORÍA
            $this->logActivity(
                'descargó un backup',
                [
                    'module' => 'backup',
                    'action' => 'descargar',
                    'filename' => $filename,
                ]
            );

            return response()->download($path, $filename, [
                'Content-Type' => 'application/zip',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error descargando backup:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al descargar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar backup
     */
    public function deleteBackup($filename)
    {
        try {
            $this->checkSuperAdmin();
            
            $path = storage_path('app/backups/' . $filename);
            
            $backup = Backup::where('filename', $filename)->first();
            
            if (!$backup && !File::exists($path)) {
                return response()->json(['message' => 'Backup no encontrado'], 404);
            }

            // ✅ REGISTRAR EN AUDITORÍA
            $this->logActivity(
                'eliminó un backup',
                [
                    'module' => 'backup',
                    'action' => 'eliminar',
                    'filename' => $filename,
                ]
            );

            // ✅ NOTIFICAR AL SUPER-ADMIN
            $superAdmin = User::role('super-admin')->first();
            if ($superAdmin && $backup) {
                $superAdmin->notify(new BackupDeleted($backup, auth()->user()));
            }

            if (File::exists($path)) {
                File::delete($path);
            }
            
            if ($backup) {
                $backup->delete();
            }
            
            \Log::info('🗑️ Super-Admin ' . auth()->user()->email . ' eliminó backup: ' . $filename);

            return response()->json([
                'message' => 'Backup eliminado correctamente',
                'file' => $filename,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error eliminando backup:', ['message' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ========================================== //
    // MÉTODOS PRIVADOS                          //
    // ========================================== //

    private function getDiskUsage()
    {
        $total = disk_total_space(base_path());
        $free = disk_free_space(base_path());
        $used = $total - $free;
        
        return [
            'total' => $this->formatSize($total),
            'used' => $this->formatSize($used),
            'free' => $this->formatSize($free),
            'percentage' => round(($used / $total) * 100, 2),
        ];
    }

    private function formatSize($bytes)
    {
        if ($bytes === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        
        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }

    private function addDirectoryToZip($zip, $basePath, $relativePath, $exclude = [])
    {
        $fullPath = $basePath . '/' . $relativePath;
        
        if (!File::exists($fullPath)) {
            return;
        }

        $files = File::allFiles($fullPath);
        
        foreach ($files as $file) {
            $filePath = $file->getPathname();
            $relative = str_replace($basePath . '/', '', $filePath);
            
            $excludeFile = false;
            foreach ($exclude as $excludeDir) {
                if (strpos($relative, $excludeDir . '/') === 0) {
                    $excludeFile = true;
                    break;
                }
            }
            
            if (strpos($relative, 'storage/app/backups/') === 0) {
                $excludeFile = true;
            }
            
            if (!$excludeFile) {
                $zip->addFile($filePath, $relative);
            }
        }
    }

    private function getBackupDiskUsage()
    {
        $backupPath = storage_path('app/backups');
        $totalSize = 0;
        $fileCount = 0;
        
        if (File::exists($backupPath)) {
            foreach (File::files($backupPath) as $file) {
                $totalSize += $file->getSize();
                $fileCount++;
            }
        }
        
        return [
            'total_size' => $this->formatSize($totalSize),
            'total_size_bytes' => $totalSize,
            'file_count' => $fileCount,
        ];
    }
}