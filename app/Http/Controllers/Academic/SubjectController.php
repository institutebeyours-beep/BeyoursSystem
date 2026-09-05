<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    /**
     * Listar asignaturas con paginación
     */
    public function index(Request $request)
    {
        try {
            $query = Subject::query();

            if ($request->has('search') && $request->search) {
                $query->where('name', 'LIKE', "%{$request->search}%")
                      ->orWhere('code', 'LIKE', "%{$request->search}%");
            }

            $subjects = $query->orderBy('name')->paginate(15);
            return response()->json($subjects);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cargar asignaturas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ OBTENER TODAS LAS ASIGNATURAS (SIN PAGINACIÓN) - CORREGIDO
     */
    public function all()
    {
        try {
            $subjects = Subject::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'credits', 'is_active']);
            
            return response()->json([
                'subjects' => $subjects
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en SubjectController@all:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al cargar asignaturas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear asignatura
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:subjects',
                'credits' => 'nullable|integer|min:0',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $subject = Subject::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'credits' => $request->credits ?? 0,
                'is_active' => $request->is_active ?? true,
            ]);

            return response()->json([
                'message' => 'Asignatura creada exitosamente',
                'subject' => $subject
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error en SubjectController@store:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al crear asignatura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar una asignatura
     */
    public function show($id)
    {
        try {
            $subject = Subject::with('courses')->findOrFail($id);
            return response()->json(['subject' => $subject]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Asignatura no encontrada'
            ], 404);
        }
    }

    /**
     * Actualizar asignatura
     */
    public function update(Request $request, $id)
    {
        try {
            $subject = Subject::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:subjects,code,' . $id,
                'credits' => 'nullable|integer|min:0',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $subject->update($request->all());

            return response()->json([
                'message' => 'Asignatura actualizada exitosamente',
                'subject' => $subject
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en SubjectController@update:', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al actualizar asignatura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ ELIMINAR ASIGNATURA - CORREGIDO
     */
    public function destroy($id)
    {
        try {
            $subject = Subject::findOrFail($id);
            
            // Verificar si tiene configuraciones asociadas
            if ($subject->gradeConfigurations()->count() > 0) {
                return response()->json([
                    'message' => 'No se puede eliminar la asignatura porque tiene configuraciones de calificaciones asociadas'
                ], 422);
            }

            // Verificar si está asignada a cursos
            if ($subject->courses()->count() > 0) {
                return response()->json([
                    'message' => 'No se puede eliminar la asignatura porque está asignada a uno o más cursos'
                ], 422);
            }

            $subject->delete();

            return response()->json([
                'message' => 'Asignatura eliminada exitosamente'
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json([
                    'message' => 'No se puede eliminar la asignatura porque está siendo utilizada en otras tablas'
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error en SubjectController@destroy:', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al eliminar asignatura: ' . $e->getMessage()
            ], 500);
        }
    }
}