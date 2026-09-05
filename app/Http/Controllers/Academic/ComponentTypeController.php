<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\ComponentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComponentTypeController extends Controller
{
    /**
     * Obtener todos los tipos de componente activos
     */
    public function index()
    {
        try {
            $types = ComponentType::active()
                ->ordered()
                ->get();

            return response()->json([
                'types' => $types
            ]);
        } catch (\Exception $e) {
            \Log::error('Error cargando tipos de componente:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al cargar los tipos de componente'
            ], 500);
        }
    }

    /**
     * Obtener todos los tipos (incluyendo inactivos) - para administración
     */
    public function all()
    {
        try {
            $types = ComponentType::ordered()->get();

            return response()->json([
                'types' => $types
            ]);
        } catch (\Exception $e) {
            \Log::error('Error cargando todos los tipos:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al cargar todos los tipos de componente'
            ], 500);
        }
    }

    /**
     * ✅ OBTENER UN TIPO POR ID - CORREGIDO
     */
    public function show($id)
    {
        try {
            $type = ComponentType::withCount('components')->findOrFail($id);
            
            return response()->json([
                'type' => $type
            ]);
        } catch (\Exception $e) {
            \Log::error('Error cargando tipo:', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Tipo no encontrado'
            ], 404);
        }
    }

    /**
     * Crear un nuevo tipo de componente
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:component_types',
            'slug' => 'required|string|max:100|unique:component_types|regex:/^[a-z0-9\-_]+$/',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $type = ComponentType::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'icon' => $request->icon ?? '📌',
                'color' => $request->color ?? 'gray',
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
                'sort_order' => ComponentType::max('sort_order') + 1,
            ]);

            return response()->json([
                'message' => 'Tipo de componente creado exitosamente',
                'type' => $type
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error creando tipo:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al crear el tipo de componente'
            ], 500);
        }
    }

    /**
     * Actualizar un tipo de componente
     */
    public function update(Request $request, $id)
    {
        try {
            $type = ComponentType::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100|unique:component_types,name,' . $id,
                'slug' => 'required|string|max:100|unique:component_types,slug,' . $id . '|regex:/^[a-z0-9\-_]+$/',
                'icon' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:500',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $type->update($request->all());

            return response()->json([
                'message' => 'Tipo de componente actualizado exitosamente',
                'type' => $type->fresh()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error actualizando tipo:', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al actualizar el tipo de componente'
            ], 500);
        }
    }

    /**
     * Eliminar un tipo de componente
     */
    public function destroy($id)
    {
        try {
            $type = ComponentType::findOrFail($id);
            
            // Verificar si tiene componentes asociados
            if ($type->components()->count() > 0) {
                return response()->json([
                    'message' => 'No se puede eliminar el tipo porque tiene componentes asociados',
                    'components_count' => $type->components()->count()
                ], 422);
            }

            $type->delete();

            return response()->json([
                'message' => 'Tipo de componente eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error eliminando tipo:', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al eliminar el tipo de componente'
            ], 500);
        }
    }
}