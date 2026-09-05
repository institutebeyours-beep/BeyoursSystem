<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Backup;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Verificar que el usuario es Super-Admin
     */
    private function checkSuperAdmin()
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'Solo el Super-Admin puede acceder al dashboard');
        }
    }

    /**
     * Obtener todas las estadísticas
     */
    public function stats()
    {
        $this->checkSuperAdmin();

        return response()->json([
            'users' => $this->getUsersStats(),
            'backups' => $this->getBackupsStats(),
            'system' => $this->getSystemStats(),
            'activity' => $this->getActivityStats(),
            'security' => $this->getSecurityStats(),
        ]);
    }

    /**
     * Estadísticas de usuarios
     */
    private function getUsersStats()
    {
        $total = User::count();
        $active = User::where('is_active', true)->count();
        $inactive = $total - $active;
        $newToday = User::whereDate('created_at', Carbon::today())->count();
        $newThisWeek = User::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();

        $lastRegistered = User::latest('created_at')->first();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'new_today' => $newToday,
            'new_this_week' => $newThisWeek,
            'last_registered' => $lastRegistered ? [
                'name' => $lastRegistered->name,
                'email' => $lastRegistered->email,
                'created_at' => $lastRegistered->created_at->diffForHumans(),
            ] : null,
        ];
    }

    /**
     * Estadísticas de backups
     */
    private function getBackupsStats()
    {
        $total = Backup::count();
        $lastBackup = Backup::latest('created_at')->first();
        $totalSize = $this->getBackupsTotalSize();

        // Backups por mes (últimos 6 meses)
        $backupsByMonth = Backup::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as total')
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(6))
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get()
        ->map(function ($item) {
            $date = Carbon::createFromDate($item->year, $item->month, 1);
            return [
                'month' => $date->format('M Y'),
                'total' => $item->total,
            ];
        });

        return [
            'total' => $total,
            'last' => $lastBackup ? [
                'filename' => $lastBackup->filename,
                'size' => $lastBackup->size,
                'created_at' => $lastBackup->created_at->diffForHumans(),
            ] : null,
            'total_size' => $totalSize,
            'by_month' => $backupsByMonth,
            'has_backups' => $total > 0,
        ];
    }

    /**
     * Estadísticas del sistema
     */
    private function getSystemStats()
    {
        $diskUsage = $this->getDiskUsage();

        return [
            'maintenance_mode' => SettingsHelper::get('maintenance_mode', false),
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'disk_usage' => $diskUsage,
            'database_size' => $this->getDatabaseSize(),
            'logs_size' => $this->getLogsSize(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'app_env' => app()->environment(),
            'app_debug' => config('app.debug') ? 'Activado' : 'Desactivado',
        ];
    }

    /**
     * Estadísticas de actividad reciente
     */
    private function getActivityStats()
    {
        $last24Hours = Backup::where('created_at', '>=', Carbon::now()->subHours(24))->count();
        $lastWeek = Backup::where('created_at', '>=', Carbon::now()->subWeek())->count();

        $lastActivity = Backup::latest('created_at')->first();

        return [
            'backups_last_24h' => $last24Hours,
            'backups_last_week' => $lastWeek,
            'last_activity' => $lastActivity ? $lastActivity->created_at->diffForHumans() : 'Ninguna',
            'last_activity_date' => $lastActivity ? $lastActivity->created_at->toDateTimeString() : null,
        ];
    }

    /**
     * Estadísticas de seguridad
     */
    private function getSecurityStats()
    {
        return [
            'maintenance_mode' => SettingsHelper::get('maintenance_mode', false),
            '2fa_required' => SettingsHelper::get('2fa_required', false),
            'blocked_ips' => SettingsHelper::get('maintenance_block_ips', ''),
            'allowed_ips' => SettingsHelper::get('maintenance_allow_ips', ''),
        ];
    }

    /**
     * Obtener tamaño total de backups
     */
    private function getBackupsTotalSize()
    {
        $backupPath = storage_path('app/backups');
        $totalSize = 0;

        if (File::exists($backupPath)) {
            foreach (File::files($backupPath) as $file) {
                $totalSize += $file->getSize();
            }
        }

        return $this->formatSize($totalSize);
    }

    /**
     * Obtener uso de disco
     */
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

    /**
     * Obtener tamaño de la base de datos
     */
    private function getDatabaseSize()
    {
        $database = config('database.connections.mysql.database');
        
        try {
            $result = DB::select("SELECT SUM(data_length + index_length) as size 
                                   FROM information_schema.tables 
                                   WHERE table_schema = ?", [$database]);
            return $this->formatSize($result[0]->size ?? 0);
        } catch (\Exception $e) {
            return '0 B';
        }
    }

    /**
     * Obtener tamaño de los logs
     */
    private function getLogsSize()
    {
        $logPath = storage_path('logs');
        $totalSize = 0;

        if (File::exists($logPath)) {
            foreach (File::files($logPath) as $file) {
                $totalSize += $file->getSize();
            }
        }

        return $this->formatSize($totalSize);
    }

    /**
     * Formatear tamaño de archivo
     */
    private function formatSize($bytes)
    {
        if ($bytes === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        
        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }
}