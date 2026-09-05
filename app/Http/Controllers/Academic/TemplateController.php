<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\TemplateType;
use App\Models\TemplateSemester;
use App\Models\TemplateSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TemplateController extends Controller
{
    /**
     * Listar todas las plantillas
     */
    public function index(Request $request)
    {
        try {
            $query = TemplateType::with(['educationType', 'semesters']);

            // ✅ Filtrar por usuario (académico solo ve las suyas + las predeterminadas)
            if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('admin')) {
                $query->where(function ($q) {
                    $q->where('created_by', auth()->id())
                      ->orWhere('is_default', true);
                });
            }

            if ($request->has('search') && $request->search) {
                $query->where('name', 'LIKE', "%{$request->search}%")
                      ->orWhere('code', 'LIKE', "%{$request->search}%");
            }

            if ($request->has('education_type_id') && $request->education_type_id) {
                $query->where('education_type_id', $request->education_type_id);
            }

            if ($request->has('is_default')) {
                $query->where('is_default', $request->is_default);
            }

            $templates = $query->orderBy('name')->paginate(15);

            return response()->json([
                'templates' => $templates
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en TemplateController@index:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al cargar las plantillas'
            ], 500);
        }
    }

    /**
     * Obtener todas las plantillas (sin paginación) - para selects
     */
    public function all()
    {
        try {
            $query = TemplateType::with(['educationType', 'semesters.subjects'])
                ->active();

            // ✅ Filtrar por usuario
            if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('admin')) {
                $query->where(function ($q) {
                    $q->where('created_by', auth()->id())
                      ->orWhere('is_default', true);
                });
            }

            $templates = $query->orderBy('name')->get();

            return response()->json([
                'templates' => $templates
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cargar las plantillas'
            ], 500);
        }
    }

    /**
     * Crear una nueva plantilla (Académico puede crear)
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'code' => 'required|string|max:20|unique:template_types',
                'description' => 'nullable|string',
                'education_type_id' => 'nullable|exists:education_types,id',
                'is_default' => 'nullable|boolean',
                'semesters' => 'required|array|min:1',
                'semesters.*.number' => 'required|integer|min:1',
                'semesters.*.hours' => 'nullable|integer|min:0',
                'semesters.*.credits' => 'nullable|integer|min:0',
                'semesters.*.subjects' => 'nullable|array',
                'semesters.*.subjects.*.name' => 'required|string|max:100',
                'semesters.*.subjects.*.credits' => 'nullable|integer|min:0',
                'semesters.*.subjects.*.theoretical_hours' => 'nullable|integer|min:0',
                'semesters.*.subjects.*.practical_hours' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // ✅ Solo super-admin y admin pueden crear plantillas por defecto
            $isDefault = false;
            if (auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin')) {
                $isDefault = $request->is_default ?? false;
            }

            $template = TemplateType::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'education_type_id' => $request->education_type_id,
                'is_default' => $isDefault,
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);

            // Crear semestres y sus asignaturas
            foreach ($request->semesters as $semesterData) {
                $semester = $template->semesters()->create([
                    'semester_number' => $semesterData['number'],
                    'total_hours' => $semesterData['hours'] ?? 0,
                    'total_credits' => $semesterData['credits'] ?? 0,
                    'description' => $semesterData['description'] ?? null,
                    'order' => $semesterData['number'] - 1,
                ]);

                if (isset($semesterData['subjects'])) {
                    foreach ($semesterData['subjects'] as $subjectData) {
                        $semester->subjects()->create([
                            'name' => $subjectData['name'],
                            'code' => $subjectData['code'] ?? null,
                            'credits' => $subjectData['credits'] ?? 0,
                            'theoretical_hours' => $subjectData['theoretical_hours'] ?? 0,
                            'practical_hours' => $subjectData['practical_hours'] ?? 0,
                            'description' => $subjectData['description'] ?? null,
                            'order' => $subjectData['order'] ?? 0,
                        ]);
                    }
                }
            }

            return response()->json([
                'message' => 'Plantilla creada exitosamente',
                'template' => $template->load('semesters.subjects')
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error en TemplateController@store:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al crear la plantilla: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar una plantilla
     */
    public function show($id)
    {
        try {
            $template = TemplateType::with(['educationType', 'semesters.subjects'])
                ->findOrFail($id);

            // ✅ Verificar permisos: solo puede ver si es suya, es default, o es admin
            if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('admin')) {
                if ($template->created_by != auth()->id() && !$template->is_default) {
                    return response()->json([
                        'message' => 'No tienes permiso para ver esta plantilla'
                    ], 403);
                }
            }

            return response()->json([
                'template' => $template
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Plantilla no encontrada'
            ], 404);
        }
    }

    /**
     * Vista previa de una plantilla (pública)
     */
    public function preview($id)
    {
        try {
            $template = TemplateType::with(['educationType', 'semesters.subjects'])
                ->findOrFail($id);

            return response()->json([
                'template' => $template,
                'summary' => [
                    'total_semesters' => $template->total_semesters,
                    'total_credits' => $template->total_credits,
                    'total_hours' => $template->total_hours,
                    'total_subjects' => $template->semesters->sum(function ($semester) {
                        return $semester->subjects->count();
                    }),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Plantilla no encontrada'
            ], 404);
        }
    }

    /**
     * Clonar una plantilla (Académico puede clonar)
     */
    public function clone($id, Request $request)
    {
        try {
            $original = TemplateType::with(['semesters.subjects'])->findOrFail($id);

            // ✅ Verificar permisos para clonar
            if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('admin')) {
                if ($original->created_by != auth()->id() && !$original->is_default) {
                    return response()->json([
                        'message' => 'No tienes permiso para clonar esta plantilla'
                    ], 403);
                }
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'code' => 'required|string|max:20|unique:template_types',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $clone = TemplateType::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $original->description,
                'education_type_id' => $original->education_type_id,
                'is_default' => false,
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);

            // Clonar semestres y asignaturas
            foreach ($original->semesters as $semester) {
                $newSemester = $clone->semesters()->create([
                    'semester_number' => $semester->semester_number,
                    'total_hours' => $semester->total_hours,
                    'total_credits' => $semester->total_credits,
                    'description' => $semester->description,
                    'order' => $semester->order,
                ]);

                foreach ($semester->subjects as $subject) {
                    $newSemester->subjects()->create([
                        'name' => $subject->name,
                        'code' => $subject->code,
                        'credits' => $subject->credits,
                        'theoretical_hours' => $subject->theoretical_hours,
                        'practical_hours' => $subject->practical_hours,
                        'description' => $subject->description,
                        'order' => $subject->order,
                    ]);
                }
            }

            return response()->json([
                'message' => 'Plantilla clonada exitosamente',
                'template' => $clone->load('semesters.subjects')
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error en TemplateController@clone:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al clonar la plantilla: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar una plantilla (solo el creador o admin)
     */
    public function update(Request $request, $id)
    {
        try {
            $template = TemplateType::findOrFail($id);

            // ✅ Verificar permisos: solo el creador o admin pueden editar
            if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('admin')) {
                if ($template->created_by != auth()->id()) {
                    return response()->json([
                        'message' => 'No tienes permiso para editar esta plantilla'
                    ], 403);
                }
            }

            // Si es una plantilla por defecto, solo admin puede modificarla
            if ($template->is_default && !auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'message' => 'No tienes permiso para modificar una plantilla por defecto'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'code' => 'required|string|max:20|unique:template_types,code,' . $id,
                'description' => 'nullable|string',
                'education_type_id' => 'nullable|exists:education_types,id',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $template->update([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'education_type_id' => $request->education_type_id,
                'is_active' => $request->is_active ?? $template->is_active,
            ]);

            return response()->json([
                'message' => 'Plantilla actualizada exitosamente',
                'template' => $template
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en TemplateController@update:', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al actualizar la plantilla: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una plantilla (solo el creador o admin)
     */
    public function destroy($id)
    {
        try {
            $template = TemplateType::findOrFail($id);

            // ✅ Verificar permisos
            if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('admin')) {
                if ($template->created_by != auth()->id()) {
                    return response()->json([
                        'message' => 'No tienes permiso para eliminar esta plantilla'
                    ], 403);
                }
            }

            // No se puede eliminar una plantilla por defecto
            if ($template->is_default) {
                return response()->json([
                    'message' => 'No se puede eliminar una plantilla por defecto'
                ], 422);
            }

            $template->delete();

            return response()->json([
                'message' => 'Plantilla eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en TemplateController@destroy:', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error al eliminar la plantilla: ' . $e->getMessage()
            ], 500);
        }
    }
}