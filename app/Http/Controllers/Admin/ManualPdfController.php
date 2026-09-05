<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class ManualPdfController extends Controller
{
    /**
     * Listar todos los PDFs con sus roles
     */
    public function index()
    {
        try {
            // ✅ Obtener todos los roles
            $roles = Role::orderBy('name')->get();
            
            // ✅ Obtener los PDFs existentes
            $pdfs = ManualPdf::with(['role', 'uploader'])->get();
            
            // ✅ Combinar: mostrar todos los roles y su PDF asociado (si existe)
            $result = $roles->map(function ($role) use ($pdfs) {
                $pdf = $pdfs->firstWhere('role_id', $role->id);
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $this->getRoleDisplayName($role->name),
                    'guard_name' => $role->guard_name,
                    'pdf' => $pdf ? [
                        'id' => $pdf->id,
                        'file_name' => $pdf->file_name,
                        'file_path' => $pdf->file_path,
                        'file_size' => $pdf->file_size,
                        'formatted_size' => $pdf->formatted_size,
                        'version' => $pdf->version,
                        'is_active' => $pdf->is_active,
                        'uploaded_at' => $pdf->uploaded_at,
                        'uploader' => $pdf->uploader?->name,
                    ] : null,
                    'has_pdf' => !is_null($pdf),
                ];
            });

            return response()->json([
                'roles' => $result,
                'pdfs' => $pdfs
            ]);

        } catch (\Exception $e) {
            \Log::error('Error cargando PDFs:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al cargar los PDFs: ' . $e->getMessage()
            ], 500);
        }
    }
/**
 * Obtener PDF por nombre de rol (para el sidebar del usuario)
 */
public function getByRoleName($roleName)
{
    try {
        \Log::info('🔍 getByRoleName - INICIO', [
            'role_name' => $roleName,
            'user_id' => auth()->id()
        ]);
        
        // Buscar el rol por nombre usando Spatie
        $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
        
        if (!$role) {
            \Log::warning('⚠️ getByRoleName - Rol NO encontrado', ['role_name' => $roleName]);
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }

        \Log::info('✅ getByRoleName - Rol encontrado', [
            'role_id' => $role->id,
            'role_name' => $role->name
        ]);

        // Buscar el PDF activo para ese rol
        $pdf = ManualPdf::where('role_id', $role->id)
            ->where('is_active', true)
            ->first();

        if (!$pdf) {
            \Log::info('📭 getByRoleName - No hay PDF para el rol', [
                'role_id' => $role->id,
                'role_name' => $roleName
            ]);
            return response()->json([
                'message' => 'No hay manual disponible para este rol'
            ], 404);
        }

        \Log::info('✅ getByRoleName - PDF encontrado', [
            'pdf_id' => $pdf->id,
            'file_name' => $pdf->file_name,
            'file_path' => $pdf->file_path
        ]);

        // ✅ Devolver JSON, NO HTML
        return response()->json($pdf);

    } catch (\Exception $e) {
        \Log::error('❌ getByRoleName - ERROR', [
            'role_name' => $roleName,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'message' => 'Error al obtener el manual: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Obtener PDF por ID de rol (para administración)
     */
    public function getByRole($roleId)
    {
        try {
            $pdf = ManualPdf::where('role_id', $roleId)
                ->where('is_active', true)
                ->with(['role', 'uploader'])
                ->first();

            if (!$pdf) {
                return response()->json([
                    'message' => 'No hay manual disponible para este rol'
                ], 404);
            }

            return response()->json($pdf);

        } catch (\Exception $e) {
            \Log::error('Error al obtener PDF por rol:', [
                'role_id' => $roleId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Error al obtener el manual'
            ], 500);
        }
    }
/**
 * Obtener PDF por ID de rol (público - para cualquier usuario autenticado)
 */
public function getPublicPdfByRole($roleId)
{
    try {
        \Log::info('🔍 getPublicPdfByRole - Inicio:', [
            'role_id' => $roleId,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email
        ]);
        
        $pdf = ManualPdf::where('role_id', $roleId)
            ->where('is_active', true)
            ->with(['role', 'uploader'])
            ->first();

        if (!$pdf) {
            \Log::info('📭 No hay PDF para el role_id:', ['role_id' => $roleId]);
            return response()->json([
                'message' => 'No hay manual disponible para este rol'
            ], 404);
        }

        \Log::info('✅ PDF encontrado:', [
            'file_name' => $pdf->file_name,
            'file_path' => $pdf->file_path
        ]);

        return response()->json($pdf);

    } catch (\Exception $e) {
        \Log::error('Error en getPublicPdfByRole:', [
            'role_id' => $roleId,
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'message' => 'Error al obtener el manual'
        ], 500);
    }
}
    /**
     * Subir un nuevo PDF para un rol
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
            'pdf' => 'required|file|mimes:pdf|max:10240', // 10MB
            'version' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $roleId = $request->role_id;
            $file = $request->file('pdf');
            $role = Role::findOrFail($roleId);

            // ✅ Eliminar PDF anterior si existe
            $existing = ManualPdf::where('role_id', $roleId)->first();
            if ($existing) {
                if (Storage::disk('public')->exists($existing->file_path)) {
                    Storage::disk('public')->delete($existing->file_path);
                }
                $existing->delete();
            }

            // ✅ Guardar nuevo archivo
            $fileName = 'manual-' . $role->name . '-' . time() . '.pdf';
            $filePath = $file->storeAs('manuals', $fileName, 'public');

            // ✅ Guardar en base de datos
            $pdf = ManualPdf::create([
                'role_id' => $roleId,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'version' => $request->version ?? '1.0',
                'is_active' => true,
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now(),
            ]);

            \Log::info('✅ PDF subido exitosamente:', [
                'role_id' => $roleId,
                'role_name' => $role->name,
                'file_name' => $pdf->file_name,
                'file_path' => $pdf->file_path
            ]);

            return response()->json([
                'message' => 'PDF subido exitosamente para el rol: ' . $role->name,
                'pdf' => $pdf->load('role', 'uploader')
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error subiendo PDF:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al subir el PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un PDF
     */
    public function destroy($id)
    {
        try {
            $pdf = ManualPdf::findOrFail($id);
            
            // ✅ Eliminar archivo físico
            if (Storage::disk('public')->exists($pdf->file_path)) {
                Storage::disk('public')->delete($pdf->file_path);
            }
            
            $pdf->delete();

            \Log::info('✅ PDF eliminado:', [
                'id' => $id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'message' => 'PDF eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error eliminando PDF:', [
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Error al eliminar el PDF'
            ], 500);
        }
    }

    /**
     * Descargar PDF por rol (nombre del rol)
     */
    public function download($roleName)
    {
        try {
            \Log::info('📥 download - Inicio:', [
                'role_name' => $roleName,
                'user_id' => auth()->id()
            ]);
            
            // ✅ Buscar el rol por nombre
            $role = Role::where('name', $roleName)->first();
            
            if (!$role) {
                \Log::warning('⚠️ download - Rol no encontrado:', ['role_name' => $roleName]);
                return response()->json([
                    'message' => 'Rol no encontrado'
                ], 404);
            }

            // ✅ Buscar el PDF asociado
            $pdf = ManualPdf::where('role_id', $role->id)
                ->where('is_active', true)
                ->first();
            
            if (!$pdf) {
                \Log::info('📭 download - PDF no disponible:', [
                    'role_name' => $roleName,
                    'role_id' => $role->id
                ]);
                return response()->json([
                    'message' => 'PDF no disponible para este rol'
                ], 404);
            }

            // ✅ Verificar que el archivo existe
            if (!Storage::disk('public')->exists($pdf->file_path)) {
                \Log::warning('⚠️ download - Archivo no existe:', [
                    'file_path' => $pdf->file_path
                ]);
                $pdf->update(['is_active' => false]);
                return response()->json([
                    'message' => 'El archivo PDF no está disponible'
                ], 404);
            }

            \Log::info('✅ download - PDF encontrado:', [
                'file_name' => $pdf->file_name,
                'file_path' => $pdf->file_path
            ]);

            // ✅ Descargar
            return Storage::disk('public')->download(
                $pdf->file_path,
                'manual-' . $role->name . '.pdf',
                ['Content-Type' => 'application/pdf']
            );

        } catch (\Exception $e) {
            \Log::error('❌ download - Error:', [
                'role_name' => $roleName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al descargar el PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener roles disponibles (para el selector)
     */
    public function getRoles()
    {
        try {
            $roles = Role::orderBy('name')->get()->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $this->getRoleDisplayName($role->name),
                    'guard_name' => $role->guard_name,
                ];
            });

            return response()->json([
                'roles' => $roles
            ]);

        } catch (\Exception $e) {
            \Log::error('Error cargando roles:', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Error al cargar los roles'
            ], 500);
        }
    }

    /**
     * Helper: Obtener display name del rol
     */
    private function getRoleDisplayName($roleName)
    {
        $displayNames = [
            'super-admin' => '👑 Super Administrador',
            'admin' => '🔧 Administrador',
            'academico' => '📚 Académico',
            'docente' => '👨‍🏫 Docente',
            'estudiante' => '👨‍🎓 Estudiante',
        ];
        return $displayNames[$roleName] ?? $roleName;
    }
}