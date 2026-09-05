<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Attendance;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Reporte de cursos
     */
    public function courses(Request $request)
    {
        $query = Course::withCount(['students'])
            ->with(['students']);

        // Filtros
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $courses = $query->get();

        // Estadísticas por curso
        $stats = $courses->map(function ($course) {
            return [
                'id' => $course->id,
                'name' => $course->name,
                'code' => $course->code,
                'status' => $course->status,
                'total_students' => $course->students_count,
                'capacity' => $course->capacity,
                'capacity_used' => round(($course->students_count / $course->capacity) * 100, 2),
                'average_grade' => round($course->students()->avg('grade_final'), 2),
                'attendance_rate' => $this->getCourseAttendanceRate($course->id),
            ];
        });

        return response()->json([
            'courses' => $stats,
            'total' => $courses->count(),
        ]);
    }

    /**
     * Reporte de estudiantes
     */
    public function students(Request $request)
    {
        $query = Student::with(['user', 'grades', 'attendances']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $students = $query->get();

        // Estadísticas por estudiante
        $stats = $students->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->full_name,
                'email' => $student->email,
                'code' => $student->code,
                'status' => $student->status,
                'total_courses' => $student->grades()->count(),
                'average_grade' => round($student->grades()->avg('grade_final'), 2),
                'attendance_rate' => $student->getAttendancePercentageAttribute(),
                'completed_courses' => $student->grades()->where('grade_final', '>=', 60)->count(),
            ];
        });

        return response()->json([
            'students' => $stats,
            'total' => $students->count(),
        ]);
    }

    /**
     * Reporte general
     */
    public function general()
    {
        $totalCourses = Course::count();
        $totalStudents = Student::count();
        $totalGrades = Grade::count();
        $totalAttendance = Attendance::count();

        $averageGrade = Grade::avg('grade_final');
        $attendanceRate = Attendance::where('status', 'present')->count();
        $attendanceTotal = Attendance::count();

        $stats = [
            'courses' => [
                'total' => $totalCourses,
                'active' => Course::where('status', 'active')->count(),
                'inactive' => Course::where('status', 'inactive')->count(),
            ],
            'students' => [
                'total' => $totalStudents,
                'active' => Student::where('status', 'active')->count(),
                'inactive' => Student::where('status', 'inactive')->count(),
                'graduated' => Student::where('status', 'graduated')->count(),
            ],
            'grades' => [
                'total' => $totalGrades,
                'average' => round($averageGrade, 2),
                'approved' => Grade::where('grade_final', '>=', 60)->count(),
                'failed' => Grade::where('grade_final', '<', 60)->whereNotNull('grade_final')->count(),
            ],
            'attendance' => [
                'total' => $totalAttendance,
                'rate' => $attendanceTotal > 0 ? round(($attendanceRate / $attendanceTotal) * 100, 2) : 0,
                'present' => Attendance::where('status', 'present')->count(),
                'absent' => Attendance::where('status', 'absent')->count(),
                'justified' => Attendance::where('status', 'justified')->count(),
            ],
        ];

        return response()->json($stats);
    }

    /**
     * Obtener tasa de asistencia de un curso
     */
    private function getCourseAttendanceRate($courseId)
    {
        $total = Attendance::where('course_id', $courseId)->count();
        if ($total === 0) return 0;

        $present = Attendance::where('course_id', $courseId)
            ->where('status', 'present')
            ->count();

        return round(($present / $total) * 100, 2);
    }
}