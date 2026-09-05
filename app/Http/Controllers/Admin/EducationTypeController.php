<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EducationType;  // ✅ Asegurar que esta línea existe
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EducationTypeController extends Controller
{
    /**
     * Listar todos los tipos de enseñanza
     */
    public function index(Request $request)
    {
        try {
            $query = EducationType::query();

            if ($request->has('search') && $request->search) {
                $query->where('name', 'LIKE', "%{$request->search}%")
                      ->orWhere('code', 'LIKE', "%{$request->search}%");
            }

            if ($request->has('active') && $request->active !== '') {
                $query->where('is_active', $request->active);
            }

            $types = $query->ordered()->paginate(15);

            return response()->json([
                'types' => $types
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en EducationTypeController@index:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al cargar los tipos de enseñanza'
            ], 500);
        }
    }
/**
 * Obtener tipos de enseñanza (público - para académicos)
 */
public function public()
{
    try {
        $types = EducationType::active()->ordered()->get();

        return response()->json([
            'types' => $types
        ]);

    } catch (\Exception $e) {
        \Log::error('Error en EducationTypeController@public:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'message' => 'Error al cargar los tipos de enseñanza'
        ], 500);
    }
}
    /**
     * Obtener todos los tipos (sin paginación) - para selects
     */
    public function all()
    {
        try {
            $types = EducationType::active()->ordered()->get();

            return response()->json([
                'types' => $types
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cargar los tipos de enseñanza'
            ], 500);
        }
    }

    /**
     * Crear un nuevo tipo de enseñanza
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'code' => 'required|string|max:20|unique:education_types',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $type = EducationType::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
                'sort_order' => $request->sort_order ?? 0,
            ]);

            return response()->json([
                'message' => 'Tipo de enseñanza creado exitosamente',
                'type' => $type
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error en EducationTypeController@store:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al crear el tipo de enseñanza: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un tipo de enseñanza
     */
    public function show($id)
    {
        try {
            $type = EducationType::findOrFail($id);

            return response()->json([
                'type' => $type
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Tipo de enseñanza no encontrado'
            ], 404);
        }
    }

    /**
     * Actualizar un tipo de enseñanza
     */
    public function update(Request $request, $id)
    {
        try {
            $type = EducationType::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'code' => 'required|string|max:20|unique:education_types,code,' . $id,
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $type->update($request->all());

            return response()->json([
                'message' => 'Tipo de enseñanza actualizado exitosamente',
                'type' => $type
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en EducationTypeController@update:', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al actualizar el tipo de enseñanza: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un tipo de enseñanza
     */
    public function destroy($id)
    {
        try {
            $type = EducationType::findOrFail($id);

            // Verificar si tiene carreras asociadas
            if ($type->careers()->count() > 0) {
                return response()->json([
                    'message' => 'No se puede eliminar el tipo porque tiene carreras asociadas'
                ], 422);
            }

            $type->delete();

            return response()->json([
                'message' => 'Tipo de enseñanza eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en EducationTypeController@destroy:', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error al eliminar el tipo de enseñanza: ' . $e->getMessage()
            ], 500);
        }
    }
}