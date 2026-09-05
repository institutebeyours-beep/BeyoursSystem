<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function stats()
    {
        // 📊 Estadísticas generales
        $totalCourses = Course::count();
        $activeCourses = Course::where('status', 'active')->count();
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'active')->count();

        // 📈 Promedio de calificaciones general
        $averageGrade = Grade::avg('grade_final');

        // 📋 Inscripciones del mes actual
        $enrollmentsThisMonth = Grade::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // 🎯 Cursos con más estudiantes
        $topCourses = Grade::select('course_id', DB::raw('count(*) as total'))
            ->groupBy('course_id')
            ->with('course')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // 📊 Estudiantes con bajo rendimiento (promedio < 60)
        $lowPerformanceStudents = Grade::select('student_id', DB::raw('avg(grade_final) as average'))
            ->groupBy('student_id')
            ->having('average', '<', 60)
            ->with('student')
            ->get();

        // 📋 Últimos estudiantes registrados
        $recentStudents = Student::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ⚠️ Cursos con cupo lleno
        $fullCourses = Course::withCount('students')
            ->having('students_count', '>=', DB::raw('capacity'))
            ->get();

        // 📊 Asistencia general
        $attendanceStats = [
            'total' => Attendance::count(),
            'present' => Attendance::where('status', 'present')->count(),
            'absent' => Attendance::where('status', 'absent')->count(),
            'justified' => Attendance::where('status', 'justified')->count(),
        ];

        return response()->json([
            'total_courses' => $totalCourses,
            'active_courses' => $activeCourses,
            'total_students' => $totalStudents,
            'active_students' => $activeStudents,
            'average_grade' => round($averageGrade, 2),
            'enrollments_this_month' => $enrollmentsThisMonth,
            'top_courses' => $topCourses,
            'low_performance' => $lowPerformanceStudents,
            'recent_students' => $recentStudents,
            'full_courses' => $fullCourses,
            'attendance_stats' => $attendanceStats,
        ]);
    }
}