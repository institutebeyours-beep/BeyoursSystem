<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Coordinator;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CompleteSeeder extends Seeder
{
    public function run(): void
    {
        // ========================================== //
        // 1. CREAR ROLES                            //
        // ========================================== //
        $this->createRoles();

        // ========================================== //
        // 2. USUARIOS ADMINISTRATIVOS               //
        // ========================================== //

        // 👑 SUPER-ADMIN
        $superAdmin = $this->createUser([
            'name' => 'Super Administrador',
            'email' => 'superadmin@beyours.com',
            'password' => 'beyours123',
            'role' => 'super-admin',
        ]);

        // 👨‍💼 ADMIN
        $admin = $this->createUser([
            'name' => 'Administrador General',
            'lastname' => 'Sistema',
            'email' => 'admin@beyours.com',
            'password' => 'beyours123',
            'role' => 'admin',
        ]);

        // 📋 ENROLLER
        $enroller = $this->createUser([
            'name' => 'Inscriptor',
            'lastname' => 'Matrículas',
            'email' => 'enroller@beyours.com',
            'password' => 'beyours123',
            'role' => 'enroller',
        ]);

        // 💰 CASHIER
        $cashier = $this->createUser([
            'name' => 'Cajero',
            'lastname' => 'Finanzas',
            'email' => 'cashier@beyours.com',
            'password' => 'beyours123',
            'role' => 'cashier',
        ]);

        // 📚 COORDINADOR ACADÉMICO
        $coordinator = $this->createUser([
            'name' => 'Coordinador Académico',
            'lastname' => 'Académico',
            'email' => 'coordinador@beyours.com',
            'password' => 'beyours123',
            'role' => 'coordinador',
        ]);

        // ========================================== //
        // 3. PROFESORES                            //
        // ========================================== //

        // 👨‍🏫 Profesor 1
        $teacherUser1 = $this->createUser([
            'name' => 'Profesor',
            'lastname' => 'Matemáticas',
            'email' => 'profesor@beyours.com',
            'password' => 'beyours123',
            'role' => 'teacher',
        ]);

        Teacher::firstOrCreate(
            ['user_id' => $teacherUser1->id],
            [
                'code' => 'TCH-0001',
                'specialty' => 'Matemáticas',
                'hire_date' => now(),
                'bio' => 'Profesor con más de 10 años de experiencia en matemáticas.',
                'status' => 'active',
                'created_by' => $superAdmin->id,
            ]
        );

        // 👨‍🏫 Profesor 2
        $teacherUser2 = $this->createUser([
            'name' => 'Profesora',
            'lastname' => 'Ciencias',
            'email' => 'profesora@beyours.com',
            'password' => 'beyours123',
            'role' => 'teacher',
        ]);

        Teacher::firstOrCreate(
            ['user_id' => $teacherUser2->id],
            [
                'code' => 'TCH-0002',
                'specialty' => 'Ciencias Naturales',
                'hire_date' => now(),
                'bio' => 'Especialista en biología y química.',
                'status' => 'active',
                'created_by' => $superAdmin->id,
            ]
        );

        // ========================================== //
        // 4. APODERADOS                            //
        // ========================================== //

        // 👨‍👩‍👦 Apoderado
        $guardianUser = $this->createUser([
            'name' => 'Apoderado',
            'lastname' => 'Pérez',
            'email' => 'apoderado@beyours.com',
            'password' => 'beyours123',
            'role' => 'guardian',
        ]);

        $guardian = Guardian::firstOrCreate(
            ['user_id' => $guardianUser->id],
            [
                'relationship' => 'Padre',
                'phone' => '555-9999',
                'emergency_phone' => '555-8888',
                'address' => 'Calle Familia 123, Ciudad',
                'status' => 'active',
                'created_by' => $superAdmin->id,
            ]
        );

        // ========================================== //
        // 5. ESTUDIANTES                           //
        // ========================================== //

        // 👨‍🎓 Juan Pérez
        $studentUser1 = $this->createUser([
            'name' => 'Juan Pérez',
            'lastname' => 'Pérez',
            'email' => 'juan.perez@estudiante.com',
            'password' => 'estudiante123',
            'role' => 'student',
        ]);

        $student1 = Student::firstOrCreate(
            ['user_id' => $studentUser1->id],
            [
                'code' => 'EST-0001',
                'enrollment_date' => now(),
                'phone' => '555-1111',
                'address' => 'Calle Estudiantes 123',
                'birth_date' => '2000-05-15',
                'guardian_name' => 'María González',
                'guardian_phone' => '555-3333',
                'status' => 'active',
                'created_by' => $superAdmin->id,
            ]
        );

        // 👨‍🎓 María Rodríguez
        $studentUser2 = $this->createUser([
            'name' => 'María Rodríguez',
            'lastname' => 'Rodríguez',
            'email' => 'maria.rodriguez@estudiante.com',
            'password' => 'estudiante123',
            'role' => 'student',
        ]);

        $student2 = Student::firstOrCreate(
            ['user_id' => $studentUser2->id],
            [
                'code' => 'EST-0002',
                'enrollment_date' => now(),
                'phone' => '555-4444',
                'address' => 'Calle Universitaria 456',
                'birth_date' => '2001-08-20',
                'guardian_name' => 'Carlos Rodríguez',
                'guardian_phone' => '555-6666',
                'status' => 'active',
                'created_by' => $superAdmin->id,
            ]
        );

        // 👨‍🎓 Carlos Gómez
        $studentUser3 = $this->createUser([
            'name' => 'Carlos Gómez',
            'lastname' => 'Gómez',
            'email' => 'carlos.gomez@estudiante.com',
            'password' => 'estudiante123',
            'role' => 'student',
        ]);

        $student3 = Student::firstOrCreate(
            ['user_id' => $studentUser3->id],
            [
                'code' => 'EST-0003',
                'enrollment_date' => now(),
                'phone' => '555-7777',
                'address' => 'Calle Estudios 789',
                'birth_date' => '1999-12-10',
                'guardian_name' => 'Ana Martínez',
                'guardian_phone' => '555-9999',
                'status' => 'active',
                'created_by' => $superAdmin->id,
            ]
        );

        // ========================================== //
        // 6. RELACIONES                            //
        // ========================================== //

        // 📋 Relación Apoderado-Estudiante
        $guardian->students()->syncWithoutDetaching([
            $student1->id => [
                'relationship' => 'Padre',
                'is_primary' => true,
                'status' => 'active',
                'created_by' => $superAdmin->id,
            ]
        ]);

        // ========================================== //
        // 7. CURSOS (si no existen)                //
        // ========================================== //

        $course1 = Course::firstOrCreate(
            ['code' => 'MAT-101'],
            [
                'name' => 'Matemáticas I',
                'description' => 'Curso de matemáticas básicas',
                'credits' => 4,
                'duration' => 40,
                'capacity' => 30,
                'status' => 'active',
                'created_by' => $superAdmin->id,
            ]
        );

        $course2 = Course::firstOrCreate(
            ['code' => 'CIE-101'],
            [
                'name' => 'Ciencias Naturales I',
                'description' => 'Curso de ciencias naturales',
                'credits' => 4,
                'duration' => 40,
                'capacity' => 30,
                'status' => 'active',
                'created_by' => $superAdmin->id,
            ]
        );

        // ========================================== //
        // 8. ASIGNAR PROFESORES A CURSOS           //
        // ========================================== //

        $teacher1 = Teacher::where('code', 'TCH-0001')->first();
        $teacher2 = Teacher::where('code', 'TCH-0002')->first();

        if ($teacher1 && $course1) {
            $teacher1->courses()->syncWithoutDetaching([
                $course1->id => [
                    'year' => date('Y'),
                    'semester' => 1,
                    'status' => 'active',
                    'created_by' => $superAdmin->id,
                ]
            ]);
        }

        if ($teacher2 && $course2) {
            $teacher2->courses()->syncWithoutDetaching([
                $course2->id => [
                    'year' => date('Y'),
                    'semester' => 1,
                    'status' => 'active',
                    'created_by' => $superAdmin->id,
                ]
            ]);
        }

        // ========================================== //
        // 9. MENSAJE FINAL                         //
        // ========================================== //
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('✅ DATOS COMPLETOS CREADOS EXITOSAMENTE');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('');
        $this->command->info('📊 ESTADÍSTICAS:');
        $this->command->info('   👑 Administrativos: 5');
        $this->command->info('   👨‍🏫 Profesores: 2');
        $this->command->info('   👨‍👩‍👦 Apoderados: 1');
        $this->command->info('   👨‍🎓 Estudiantes: 3');
        $this->command->info('   📚 Cursos: 2');
        $this->command->info('   📋 Relaciones: 1 (Apoderado-Estudiante)');
        $this->command->info('   📋 Asignaciones: 2 (Profesor-Curso)');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }

    private function createUser(array $data)
    {
        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'uuid' => Str::uuid(),
                'name' => $data['name'],
                'lastname' => $data['lastname'] ?? null,
                'second_lastname' => $data['second_lastname'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'address' => $data['address'] ?? null,
                'profile_image' => null,
                'phone' => $data['phone'] ?? null,
                'cellphone' => $data['cellphone'] ?? null,
                'email_verified_at' => now(),
                'password' => bcrypt($data['password']),
                'is_active' => true,
                'last_login_at' => null,
                'two_factor_secret' => null,
                'two_factor_confirmed_at' => null,
                'two_factor_temp_token' => null,
                'two_factor_recovery_codes' => null,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        }

        return $user;
    }

    private function createRoles()
    {
        $roles = [
            'super-admin',
            'admin',
            'enroller',
            'cashier',
            'coordinador',
            'teacher',
            'tutor',
            'guardian',
            'student',
            'user',
        ];
        
        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web'
            ]);
        }
    }
}