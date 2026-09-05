<?php

namespace App\Traits;

use Spatie\Activitylog\Facades\Activity;

trait LogsAudit
{
    protected function logAudit($description, $properties = [])
    {
        try {
            $log = Activity::causedBy(auth()->user())
                ->withProperties(array_merge([
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'url' => request()->fullUrl(),
                ], $properties))
                ->log($description); // ✅ $description es string, no array
                
            \Log::info('📝 Auditoría registrada:', [
                'description' => $description,
                'user' => auth()->user()?->email,
                'ip' => request()->ip(),
            ]);
            
            return $log;
            
        } catch (\Exception $e) {
            \Log::error('❌ Error en auditoría:', [
                'message' => $e->getMessage(),
                'description' => $description,
            ]);
            return null;
        }
    }

    protected function logMaintenance($action, $details = [])
    {
        return $this->logAudit("Mantenimiento: {$action}", array_merge([
            'module' => 'maintenance',
            'action' => $action,
        ], $details));
    }

    protected function logBackup($action, $details = [])
    {
        return $this->logAudit("Backup: {$action}", array_merge([
            'module' => 'backup',
            'action' => $action,
        ], $details));
    }

    protected function logSecurity($action, $details = [])
    {
        return $this->logAudit("Seguridad: {$action}", array_merge([
            'module' => 'security',
            'action' => $action,
        ], $details));
    }
}