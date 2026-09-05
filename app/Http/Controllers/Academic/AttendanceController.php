<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Listar asistencia
     */
    public function index(Request $request)
    {
        $query = Attendance::with(['student.user', 'course', 'creator']);

        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->date) {
            $query->whereDate('date', $request->date);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $perPage = $request->per_page ?? 20;
        $attendance = $query->orderBy('date', 'desc')->paginate($perPage);

        return response()->json($attendance);
    }

    /**
     * Registrar asistencia
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,justified',
            'attendance.*.observation' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $course = Course::find($request->course_id);
        $date = $request->date;

        // Verificar que no haya asistencia ya registrada para esta fecha y curso
        $existing = Attendance::where('course_id', $course->id)
            ->whereDate('date', $date)
            ->count();

        if ($existing > 0) {
            return response()->json([
                'message' => 'Ya existe asistencia registrada para este curso en esta fecha.',
            ], 422);
        }

        $attendances = [];

        foreach ($request->attendance as $data) {
            $attendance = Attendance::create([
                'student_id' => $data['student_id'],
                'course_id' => $course->id,
                'date' => $date,
                'status' => $data['status'],
                'observation' => $data['observation'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $attendances[] = $attendance;
        }

        return response()->json([
            'message' => 'Asistencia registrada exitosamente',
            'attendances' => $attendances,
        ]);
    }

    /**
     * Reporte de asistencia
     */
    public function reports(Request $request)
    {
        $courseId = $request->course_id;
        $studentId = $request->student_id;

        $query = Attendance::with(['student.user', 'course']);

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        $attendances = $query->get();

        // Estadísticas
        $stats = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'justified' => $attendances->where('status', 'justified')->count(),
            'attendance_rate' => 0,
        ];

        if ($stats['total'] > 0) {
            $stats['attendance_rate'] = round(($stats['present'] / $stats['total']) * 100, 2);
        }

        return response()->json([
            'attendances' => $attendances,
            'stats' => $stats,
        ]);
    }
}