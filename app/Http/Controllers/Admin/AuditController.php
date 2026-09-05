<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    /**
     * Verificar que el usuario es Super-Admin
     */
    private function checkSuperAdmin()
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'Solo el Super-Admin puede ver la auditoría');
        }
    }

    /**
     * Listar logs de auditoría
     */
    public function index(Request $request)
    {
        $this->checkSuperAdmin();
        
        $query = Activity::with('causer')
            ->orderBy('created_at', 'desc');
        
        // Filtro por búsqueda
        if ($request->search) {
            $query->where('description', 'LIKE', "%{$request->search}%");
        }
        
        // Filtro por módulo
        if ($request->module) {
            $query->where('properties->module', $request->module);
        }
        
        // Filtro por acción
        if ($request->action) {
            $query->where('description', 'LIKE', "%{$request->action}%");
        }
        
        $perPage = $request->per_page ?? 20;
        $logs = $query->paginate($perPage);
        
        return response()->json($logs);
    }

    /**
     * Ver un log específico
     */
    public function show($id)
    {
        $this->checkSuperAdmin();
        
        $log = Activity::with('causer')->findOrFail($id);
        return response()->json($log);
    }

    /**
     * Eliminar un log específico
     */
    public function destroy($id)
    {
        $this->checkSuperAdmin();
        
        $log = Activity::findOrFail($id);
        $log->delete();
        
        return response()->json([
            'message' => 'Registro eliminado correctamente',
        ]);
    }

    /**
     * Limpiar todos los logs
     */
    public function clear()
    {
        $this->checkSuperAdmin();
        
        DB::table('activity_log')->truncate();
        
        return response()->json([
            'message' => 'Todos los registros de auditoría han sido eliminados',
        ]);
    }
}