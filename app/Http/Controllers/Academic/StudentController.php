<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    /**
     * Listar estudiantes
     */
    public function index(Request $request)
    {
        try {
            $query = Student::with(['user', 'creator']);

            // Filtros
            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('code', 'LIKE', "%{$request->search}%")
                      ->orWhereHas('user', function ($q2) use ($request) {
                          $q2->where('name', 'LIKE', "%{$request->search}%")
                             ->orWhere('email', 'LIKE', "%{$request->search}%");
                      });
                });
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $perPage = $request->per_page ?? 15;
            $students = $query->paginate($perPage);

            return response()->json($students);
            
        } catch (\Exception $e) {
            \Log::error('Error en StudentController@index:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al listar estudiantes: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Crear un estudiante
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'birth_date' => 'nullable|date',
                'guardian_name' => 'nullable|string|max:255',
                'guardian_phone' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Crear usuario
            $user = User::create([
                'uuid' => (string) Str::uuid(),
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Asignar rol de estudiante
            $user->assignRole('student');

            // Crear estudiante
            $student = Student::create([
                'user_id' => $user->id,
                'code' => $this->generateStudentCode(),
                'enrollment_date' => now(),
                'phone' => $request->phone,
                'address' => $request->address,
                'birth_date' => $request->birth_date,
                'guardian_name' => $request->guardian_name,
                'guardian_phone' => $request->guardian_phone,
                'status' => 'active',
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Estudiante creado exitosamente',
                'student' => $student,
                'user' => $user,
            ], 201);
            
        } catch (\Exception $e) {
            \Log::error('Error en StudentController@store:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al crear estudiante: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ver un estudiante específico
     */
    public function show($id)
    {
        try {
            // ✅ Buscar el estudiante con relaciones
            $student = Student::with(['user', 'grades.course'])->find($id);
            
            // ✅ Si no existe, devolver error 404
            if (!$student) {
                return response()->json([
                    'message' => 'Estudiante no encontrado'
                ], 404);
            }

            // ✅ Estadísticas básicas
            $averageGrade = $student->grades()->avg('grade_final');
            $totalCourses = $student->grades()->count();
            $completedCourses = $student->grades()->where('grade_final', '>=', 60)->count();
            
            // ✅ Calcular asistencia (con manejo de errores)
            $attendancePercentage = 0;
            try {
                // ✅ Usar el método del modelo (que ya maneja errores)
                $attendancePercentage = $student->getAttendancePercentageAttribute();
            } catch (\Exception $e) {
                // Si falla, dejar en 0
                \Log::warning('Error calculando asistencia para estudiante ' . $id);
            }

            return response()->json([
                'student' => $student,
                'stats' => [
                    'average_grade' => round((float) $averageGrade, 2),
                    'total_courses' => (int) $totalCourses,
                    'completed_courses' => (int) $completedCourses,
                    'attendance_percentage' => (float) $attendancePercentage,
                ],
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error en StudentController@show:', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al cargar el estudiante: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar un estudiante
     */
    public function update(Request $request, $id)
    {
        try {
            $student = Student::with('user')->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $student->user_id,
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'birth_date' => 'nullable|date',
                'guardian_name' => 'nullable|string|max:255',
                'guardian_phone' => 'nullable|string|max:20',
                'status' => 'nullable|in:active,inactive,graduated,suspended',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Actualizar usuario
            if ($request->has('name')) {
                $student->user->update(['name' => $request->name]);
            }

            if ($request->has('email')) {
                $student->user->update(['email' => $request->email]);
            }

            // Actualizar estudiante
            $student->update([
                'phone' => $request->phone ?? $student->phone,
                'address' => $request->address ?? $student->address,
                'birth_date' => $request->birth_date ?? $student->birth_date,
                'guardian_name' => $request->guardian_name ?? $student->guardian_name,
                'guardian_phone' => $request->guardian_phone ?? $student->guardian_phone,
                'status' => $request->status ?? $student->status,
            ]);

            return response()->json([
                'message' => 'Estudiante actualizado exitosamente',
                'student' => $student,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error en StudentController@update:', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al actualizar estudiante: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar un estudiante (soft delete)
     */
    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);

            // Verificar si tiene calificaciones
            $gradesCount = $student->grades()->count();
            if ($gradesCount > 0) {
                return response()->json([
                    'message' => 'No se puede eliminar el estudiante porque tiene calificaciones registradas.',
                ], 422);
            }

            // Eliminar el usuario asociado
            if ($student->user) {
                $student->user->delete();
            }

            $student->delete();

            return response()->json([
                'message' => 'Estudiante eliminado exitosamente',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error en StudentController@destroy:', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al eliminar estudiante: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generar código automático para el estudiante
     */
    private function generateStudentCode()
    {
        $prefix = 'EST';
        $number = Student::withTrashed()->count() + 1;
        return "{$prefix}-" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}