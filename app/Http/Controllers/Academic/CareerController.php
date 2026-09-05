<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\EducationType;
use App\Models\TemplateType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CareerController extends Controller
{
    /**
     * Listar todas las carreras
     */
    public function index(Request $request)
    {
        try {
            $query = Career::with('educationType');

            if ($request->has('search') && $request->search) {
                $query->search($request->search);
            }

            if ($request->has('education_type_id') && $request->education_type_id) {
                $query->where('education_type_id', $request->education_type_id);
            }

            if ($request->has('active') && $request->active !== '') {
                $query->where('is_active', $request->active);
            }

            $careers = $query->orderBy('name')->paginate(15);

            return response()->json([
                'careers' => $careers
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en CareerController@index:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al cargar las carreras'
            ], 500);
        }
    }

    /**
     * Obtener todas las carreras (sin paginación) - para selects
     */
    public function all(Request $request)
    {
        try {
            $query = Career::with('educationType')->active();

            if ($request->has('education_type_id') && $request->education_type_id) {
                $query->where('education_type_id', $request->education_type_id);
            }

            $careers = $query->orderBy('name')->get();

            return response()->json([
                'careers' => $careers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cargar las carreras'
            ], 500);
        }
    }

    /**
     * Crear una carrera desde plantilla
     */
   public function createFromTemplate(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:template_types,id',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:careers',
            'description' => 'nullable|string',
            'education_type_id' => 'required|exists:education_types,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $template = TemplateType::with(['semesters.subjects'])->findOrFail($request->template_id);

        // ✅ CALCULAR HORAS DESDE LA PLANTILLA
        $theoreticalHours = 0;
        $practicalHours = 0;
        $totalCredits = 0;

        foreach ($template->semesters as $semester) {
            foreach ($semester->subjects as $subject) {
                $theoreticalHours += $subject->theoretical_hours ?? 0;
                $practicalHours += $subject->practical_hours ?? 0;
                $totalCredits += $subject->credits ?? 0;
            }
        }

        \Log::info('📊 Creando carrera desde plantilla:', [
            'template' => $template->name,
            'theoretical_hours' => $theoreticalHours,
            'practical_hours' => $practicalHours,
            'total_credits' => $totalCredits,
            'semesters' => $template->semesters->count(),
        ]);

        // ✅ CREAR CARRERA CON TODOS LOS CAMPOS
        $career = Career::create([
            'education_type_id' => $request->education_type_id,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'total_credits' => $request->total_credits ?? $totalCredits,
            'theoretical_hours' => $request->theoretical_hours ?? $theoreticalHours,
            'practical_hours' => $request->practical_hours ?? $practicalHours,
            'duration_years' => $request->duration_years ?? ceil($template->semesters->count() / 2),
            'duration_semesters' => $request->duration_semesters ?? $template->semesters->count(),
            'is_active' => true,
        ]);

        \Log::info('✅ Carrera creada:', [
            'id' => $career->id,
            'name' => $career->name,
            'theoretical_hours' => $career->theoretical_hours,
            'practical_hours' => $career->practical_hours,
        ]);

        return response()->json([
            'message' => 'Carrera creada exitosamente',
            'career' => $career
        ], 201);

    } catch (\Exception $e) {
        \Log::error('Error en createFromTemplate:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'message' => 'Error al crear la carrera: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Crear una nueva carrera
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'education_type_id' => 'required|exists:education_types,id',
                'name' => 'required|string|max:100',
                'code' => 'required|string|max:20|unique:careers',
                'description' => 'nullable|string',
                'total_credits' => 'nullable|integer|min:0',
                'theoretical_hours' => 'nullable|integer|min:0',
                'practical_hours' => 'nullable|integer|min:0',
                'duration_years' => 'nullable|integer|min:0',
                'duration_semesters' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $career = Career::create([
                'education_type_id' => $request->education_type_id,
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'total_credits' => $request->total_credits ?? 0,
                'theoretical_hours' => $request->theoretical_hours ?? 0,
                'practical_hours' => $request->practical_hours ?? 0,
                'duration_years' => $request->duration_years ?? 0,
                'duration_semesters' => $request->duration_semesters ?? 0,
                'is_active' => $request->is_active ?? true,
            ]);

            return response()->json([
                'message' => 'Carrera creada exitosamente',
                'career' => $career
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error en CareerController@store:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al crear la carrera: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar una carrera
     */
    public function show($id)
    {
        try {
            $career = Career::with(['educationType', 'courses', 'students'])
                ->findOrFail($id);

            return response()->json([
                'career' => $career,
                'stats' => [
                    'total_courses' => $career->courses()->count(),
                    'total_students' => $career->students()->count(),
                    'active_students' => $career->active_students_count,
                    'used_credits' => $career->used_credits,
                    'available_credits' => $career->available_credits,
                    'credits_progress' => $career->credits_progress,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Carrera no encontrada'
            ], 404);
        }
    }

    /**
     * Actualizar una carrera
     */
    public function update(Request $request, $id)
    {
        try {
            $career = Career::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'education_type_id' => 'required|exists:education_types,id',
                'name' => 'required|string|max:100',
                'code' => 'required|string|max:20|unique:careers,code,' . $id,
                'description' => 'nullable|string',
                'total_credits' => 'nullable|integer|min:0',
                'theoretical_hours' => 'nullable|integer|min:0',
                'practical_hours' => 'nullable|integer|min:0',
                'duration_years' => 'nullable|integer|min:0',
                'duration_semesters' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $career->update($request->all());

            return response()->json([
                'message' => 'Carrera actualizada exitosamente',
                'career' => $career
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en CareerController@update:', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al actualizar la carrera: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una carrera
     */
    public function destroy($id)
    {
        try {
            $career = Career::findOrFail($id);

            // Verificar si tiene cursos asociados
            if ($career->courses()->count() > 0) {
                return response()->json([
                    'message' => 'No se puede eliminar la carrera porque tiene cursos asociados'
                ], 422);
            }

            // Verificar si tiene estudiantes asociados
            if ($career->students()->count() > 0) {
                return response()->json([
                    'message' => 'No se puede eliminar la carrera porque tiene estudiantes asociados'
                ], 422);
            }

            $career->delete();

            return response()->json([
                'message' => 'Carrera eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en CareerController@destroy:', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error al eliminar la carrera: ' . $e->getMessage()
            ], 500);
        }
    }
}