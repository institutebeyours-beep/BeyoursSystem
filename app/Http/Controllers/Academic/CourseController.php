<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Listar cursos
     */
    public function index(Request $request)
    {
        try {
            $query = Course::query();

            if ($request->search) {
                $query->where('name', 'LIKE', "%{$request->search}%")
                      ->orWhere('code', 'LIKE', "%{$request->search}%");
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->has('with_subjects') && $request->with_subjects) {
                $query->with('subjects');
            }

            $query->with('creator');
            $perPage = $request->per_page ?? 15;
            $courses = $query->orderBy('name')->paginate($perPage);

            return response()->json($courses);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cargar cursos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear un curso
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'code' => 'nullable|string|max:50|unique:courses,code',
                'description' => 'nullable|string',
                'course_type' => 'nullable|in:theoretical,theoretical_practical,practical,specialized_lab',
                'total_credits' => 'nullable|integer|min:0',
                'class_hours_per_week' => 'nullable|numeric|min:0',
                'study_hours_per_week' => 'nullable|numeric|min:0',
                'lab_hours_per_week' => 'nullable|numeric|min:0',
                'total_hours_per_week' => 'nullable|numeric|min:0',
                'total_weeks' => 'nullable|integer|min:1',
                'total_hours' => 'nullable|numeric|min:0',
                'study_ratio' => 'nullable|numeric|min:0',
                'lab_ratio' => 'nullable|numeric|min:0',
                'duration' => 'nullable|integer|min:0',
                'schedule' => 'nullable|array',
                'capacity' => 'nullable|integer|min:1',
                'status' => 'nullable|in:active,inactive,completed',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $code = $request->code ?? $this->generateCourseCode($request->name);

            $course = Course::create([
                'name' => $request->name,
                'code' => $code,
                'description' => $request->description,
                'course_type' => $request->course_type ?? 'theoretical_practical',
                'total_credits' => $request->total_credits ?? 0,
                'credits' => $request->credits ?? 0,
                'class_hours_per_week' => $request->class_hours_per_week ?? 0,
                'study_hours_per_week' => $request->study_hours_per_week ?? 0,
                'lab_hours_per_week' => $request->lab_hours_per_week ?? 0,
                'total_hours_per_week' => $request->total_hours_per_week ?? 0,
                'total_weeks' => $request->total_weeks ?? 16,
                'total_hours' => $request->total_hours ?? 0,
                'study_ratio' => $request->study_ratio ?? 2.0,
                'lab_ratio' => $request->lab_ratio ?? 0.5,
                'duration' => $request->duration,
                'schedule' => $request->schedule,
                'capacity' => $request->capacity ?? 20,
                'status' => $request->status ?? 'active',
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Curso creado exitosamente',
                'course' => $course,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear curso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver un curso específico
     */
    public function show($id)
    {
        try {
            $course = Course::with(['creator', 'students.student.user', 'subjects'])
                ->findOrFail($id);

            $totalStudents = $course->students()->count();
            $averageGrade = $course->students()->avg('grade_final');

            return response()->json([
                'course' => $course,
                'stats' => [
                    'total_students' => $totalStudents,
                    'average_grade' => round($averageGrade, 2),
                    'capacity_used' => round(($totalStudents / max($course->capacity, 1)) * 100, 2),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Curso no encontrado'], 404);
        }
    }

    /**
     * Actualizar un curso
     */
    public function update(Request $request, $id)
    {
        try {
            $course = Course::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'course_type' => 'nullable|in:theoretical,theoretical_practical,practical,specialized_lab',
                'total_credits' => 'nullable|integer|min:0',
                'class_hours_per_week' => 'nullable|numeric|min:0',
                'study_hours_per_week' => 'nullable|numeric|min:0',
                'lab_hours_per_week' => 'nullable|numeric|min:0',
                'total_hours_per_week' => 'nullable|numeric|min:0',
                'total_weeks' => 'nullable|integer|min:1',
                'total_hours' => 'nullable|numeric|min:0',
                'study_ratio' => 'nullable|numeric|min:0',
                'lab_ratio' => 'nullable|numeric|min:0',
                'duration' => 'nullable|integer|min:0',
                'schedule' => 'nullable|array',
                'capacity' => 'nullable|integer|min:1',
                'status' => 'nullable|in:active,inactive,completed',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $course->update($request->all());

            return response()->json([
                'message' => 'Curso actualizado exitosamente',
                'course' => $course,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar curso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un curso (soft delete)
     */
    public function destroy($id)
    {
        try {
            $course = Course::findOrFail($id);

            $studentsCount = $course->students()->count();
            if ($studentsCount > 0) {
                return response()->json([
                    'message' => 'No se puede eliminar el curso porque tiene estudiantes inscritos.',
                ], 422);
            }

            $course->delete();

            return response()->json([
                'message' => 'Curso eliminado exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar curso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ASIGNAR ASIGNATURA A UN CURSO (con validación de créditos)
     */
    public function assignSubject(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'course_id' => 'required|exists:courses,id',
                'subject_id' => 'required|exists:subjects,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $course = Course::findOrFail($request->course_id);
            $subject = Subject::findOrFail($request->subject_id);

            // ✅ Verificar si ya está asignado
            if ($course->subjects()->where('subject_id', $request->subject_id)->exists()) {
                return response()->json([
                    'message' => 'La asignatura ya está asignada a este curso'
                ], 422);
            }

            // ✅ Verificar créditos disponibles
            if ($course->total_credits > 0) {
                if (!$course->hasAvailableCredits($subject->credits)) {
                    return response()->json([
                        'message' => "No hay suficientes créditos disponibles. Disponibles: {$course->available_credits}, Necesarios: {$subject->credits}"
                    ], 422);
                }
            }

            $course->subjects()->attach($request->subject_id);
            $course->load('subjects');

            return response()->json([
                'message' => 'Asignatura asignada al curso exitosamente',
                'course' => $course
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al asignar asignatura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * REMOVER ASIGNATURA DE UN CURSO
     */
    public function removeSubject($courseId, $subjectId)
    {
        try {
            $course = Course::findOrFail($courseId);
            $course->subjects()->detach($subjectId);
            $course->load('subjects');

            return response()->json([
                'message' => 'Asignatura removida del curso exitosamente',
                'course' => $course
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al remover asignatura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * OBTENER ASIGNATURAS DE UN CURSO
     */
    public function getSubjects($courseId)
    {
        try {
            $course = Course::with('subjects')->findOrFail($courseId);
            return response()->json([
                'subjects' => $course->subjects
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cargar asignaturas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar código automático para el curso
     */
    private function generateCourseCode($name)
    {
        $prefix = strtoupper(substr($name, 0, 3));
        $number = Course::where('code', 'LIKE', "{$prefix}%")->count() + 1;
        return "{$prefix}-" . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}