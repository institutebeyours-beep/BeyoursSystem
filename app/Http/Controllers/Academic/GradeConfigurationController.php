<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\GradeConfiguration; // ✅ Esto es todo lo que necesitas
use App\Models\Course;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GradeConfigurationController extends Controller
{
    // ========================================== //
    // SHOW BY SUBJECT
    // ========================================== //
    public function showBySubject($subjectId)
    {
        try {
            $configuration = GradeConfiguration::where('subject_id', $subjectId)
                ->with('components')
                ->first();

            if (!$configuration) {
                return response()->json([
                    'configuration' => null,
                    'message' => 'No hay configuración para esta asignatura'
                ]);
            }

            return response()->json([
                'configuration' => $configuration
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en GradeConfigurationController@showBySubject:', [
                'subjectId' => $subjectId,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error al cargar la configuración'
            ], 500);
        }
    }

    // ========================================== //
    // SHOW BY COURSE
    // ========================================== //
    public function showByCourse($courseId)
    {
        try {
            $configuration = GradeConfiguration::where('course_id', $courseId)
                ->with('components')
                ->first();

            if (!$configuration) {
                return response()->json([
                    'configuration' => null,
                    'message' => 'No hay configuración para este curso'
                ]);
            }

            return response()->json([
                'configuration' => $configuration
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en GradeConfigurationController@showByCourse:', [
                'courseId' => $courseId,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error al cargar la configuración'
            ], 500);
        }
    }

    // ========================================== //
    // STORE
    // ========================================== //
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'course_id' => 'required|exists:courses,id',
                'subject_id' => 'nullable|exists:subjects,id',
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $existing = GradeConfiguration::where('course_id', $request->course_id)
                ->when($request->subject_id, function ($query) use ($request) {
                    return $query->where('subject_id', $request->subject_id);
                })
                ->first();

            if ($existing) {
                return response()->json([
                    'message' => 'Ya existe una configuración para este curso',
                    'configuration' => $existing
                ], 422);
            }

            $configuration = GradeConfiguration::create([
                'course_id' => $request->course_id,
                'subject_id' => $request->subject_id,
                'name' => $request->name ?? 'Configuración por defecto',
                'description' => $request->description ?? null,
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Configuración creada exitosamente',
                'configuration' => $configuration
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error en GradeConfigurationController@store:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al crear la configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========================================== //
    // UPDATE
    // ========================================== //
    public function update(Request $request, $id)
    {
        try {
            $configuration = GradeConfiguration::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'components' => 'nullable|array',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $configuration->update([
                'name' => $request->name ?? $configuration->name,
                'description' => $request->description ?? $configuration->description,
                'is_active' => $request->is_active ?? $configuration->is_active,
            ]);

            if ($request->has('components')) {
                $configuration->components()->delete();

                $order = 0;
                foreach ($request->components as $componentData) {
                    $configuration->components()->create([
                        'name' => $componentData['name'],
                        'type_id' => $componentData['type_id'] ?? 1,
                        'percentage' => $componentData['percentage'] ?? 0,
                        'max_grade' => $componentData['max_grade'] ?? 100,
                        'description' => $componentData['description'] ?? null,
                        'order' => $order,
                        'is_required' => $componentData['is_required'] ?? false,
                    ]);
                    $order++;
                }
            }

            $configuration->load('components');

            return response()->json([
                'message' => 'Configuración actualizada exitosamente',
                'configuration' => $configuration
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en GradeConfigurationController@update:', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al actualizar la configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========================================== //
    // CLONE
    // ========================================== //
    public function clone(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'source_course_id' => 'required|exists:courses,id',
                'target_course_id' => 'required|exists:courses,id|different:source_course_id',
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'replace' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $sourceConfiguration = GradeConfiguration::where('course_id', $request->source_course_id)
                ->with('components')
                ->first();

            if (!$sourceConfiguration) {
                return response()->json([
                    'message' => 'El curso origen no tiene configuración'
                ], 404);
            }

            if (!$sourceConfiguration->components || $sourceConfiguration->components->isEmpty()) {
                return response()->json([
                    'message' => 'La configuración origen no tiene componentes para clonar'
                ], 422);
            }

            DB::beginTransaction();

            try {
                $existingTarget = GradeConfiguration::where('course_id', $request->target_course_id)->first();

                if ($existingTarget && !$request->replace) {
                    return response()->json([
                        'message' => 'El curso destino ya tiene una configuración. Usa "replace": true para sobrescribir.',
                        'existing_configuration' => $existingTarget,
                        'can_replace' => true
                    ], 422);
                }

                if ($existingTarget && $request->replace) {
                    $existingTarget->delete();
                }

                $newConfiguration = GradeConfiguration::create([
                    'course_id' => $request->target_course_id,
                    'name' => $request->name ?? $sourceConfiguration->name . ' (clonado)',
                    'description' => $request->description ?? $sourceConfiguration->description,
                    'is_active' => true,
                    'created_by' => auth()->id(),
                ]);

                $clonedCount = 0;
                foreach ($sourceConfiguration->components as $component) {
                    $newConfiguration->components()->create([
                        'name' => $component->name,
                        'type_id' => $component->type_id ?? 1,
                        'percentage' => $component->percentage ?? 0,
                        'max_grade' => $component->max_grade ?? 100,
                        'description' => $component->description ?? null,
                        'order' => $component->order ?? $clonedCount,
                        'is_required' => $component->is_required ?? false,
                    ]);
                    $clonedCount++;
                }

                DB::commit();

                return response()->json([
                    'message' => $existingTarget && $request->replace ? 'Configuración reemplazada exitosamente' : 'Configuración clonada exitosamente',
                    'configuration' => $newConfiguration->load('components'),
                    'components_cloned' => $clonedCount,
                    'replaced' => $existingTarget && $request->replace,
                ], 201);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('Error en GradeConfigurationController@clone:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al clonar la configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========================================== //
    // DESTROY
    // ========================================== //
    public function destroy($id)
    {
        try {
            $configuration = GradeConfiguration::findOrFail($id);
            $configuration->delete();

            return response()->json([
                'message' => 'Configuración eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en GradeConfigurationController@destroy:', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error al eliminar la configuración: ' . $e->getMessage()
            ], 500);
        }
    }
}