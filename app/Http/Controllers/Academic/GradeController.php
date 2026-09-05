<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradeController extends Controller
{
    /**
     * Listar calificaciones
     */
    public function index(Request $request)
    {
        $query = Grade::with(['student.user', 'course', 'creator']);

        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->partial) {
            $query->where('partial', $request->partial);
        }

        $perPage = $request->per_page ?? 20;
        $grades = $query->paginate($perPage);

        return response()->json($grades);
    }

    /**
     * Registrar calificaciones de un curso
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.partial' => 'required|integer|min:1|max:3',
            'grades.*.grade' => 'nullable|numeric|min:0|max:100',
            'grades.*.observations' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $course = Course::find($request->course_id);
        $createdGrades = [];

        foreach ($request->grades as $gradeData) {
            $grade = Grade::updateOrCreate(
                [
                    'student_id' => $gradeData['student_id'],
                    'course_id' => $request->course_id,
                    'partial' => $gradeData['partial'],
                ],
                [
                    'grade' => $gradeData['grade'] ?? null,
                    'observations' => $gradeData['observations'] ?? null,
                    'created_by' => auth()->id(),
                ]
            );

            // Actualizar calificación final si todas las parciales están completas
            $this->updateFinalGrade($grade->student_id, $request->course_id);

            $createdGrades[] = $grade;
        }

        return response()->json([
            'message' => 'Calificaciones registradas exitosamente',
            'grades' => $createdGrades,
        ]);
    }

    /**
     * Ver calificaciones de un curso
     */
    public function getCourseGrades($courseId)
    {
        $course = Course::findOrFail($courseId);
        
        $grades = Grade::with(['student.user'])
            ->where('course_id', $courseId)
            ->orderBy('student_id')
            ->get()
            ->groupBy('student_id');

        return response()->json([
            'course' => $course,
            'grades' => $grades,
        ]);
    }

    /**
     * Reporte de calificaciones por curso
     */
    public function reports(Request $request)
    {
        $courseId = $request->course_id;

        $query = Grade::with(['student.user', 'course']);

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        $grades = $query->get();

        // Estadísticas por curso
        $stats = [
            'total_students' => $grades->groupBy('student_id')->count(),
            'average' => $grades->avg('grade_final'),
            'approved' => $grades->where('grade_final', '>=', 60)->count(),
            'failed' => $grades->where('grade_final', '<', 60)->whereNotNull('grade_final')->count(),
            'pending' => $grades->whereNull('grade_final')->count(),
        ];

        return response()->json([
            'grades' => $grades,
            'stats' => $stats,
        ]);
    }

    /**
     * Actualizar calificación final de un estudiante en un curso
     */
    private function updateFinalGrade($studentId, $courseId)
    {
        $grades = Grade::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->whereNotNull('grade')
            ->get();

        // Si tiene las 3 parciales, calcular promedio
        if ($grades->count() >= 3) {
            $average = $grades->avg('grade');
            
            Grade::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->whereNull('grade_final')
                ->update(['grade_final' => $average]);
        }
    }
}