<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class AcademicPermissionsSeeder extends Seeder
{
    public function run()
    {
        // ========================================== //
        // 1. LIMPIAR PERMISOS EXISTENTES            //
        // ========================================== //
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Permission::where('name', 'LIKE', 'academic_%')->delete();
        Permission::where('name', 'LIKE', 'admin_%')->delete();
        Permission::where('name', 'LIKE', 'cashier_%')->delete();
        Permission::where('name', 'LIKE', 'enroller_%')->delete();
        Permission::where('name', 'LIKE', 'student_%')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ========================================== //
        // 2. CREAR PERMISOS ACADÉMICOS              //
        // ========================================== //
        $academicPermissions = [
            // Dashboard
            'academic_dashboard_view',
            
            // Cursos
            'academic_courses_view',
            'academic_courses_create',
            'academic_courses_edit',
            'academic_courses_delete',
            
            // Estudiantes
            'academic_students_view',
            'academic_students_create',
            'academic_students_edit',
            'academic_students_delete',
            
            // Calificaciones
            'academic_grades_view',
            'academic_grades_manage',
            
            // Asistencia
            'academic_attendance_view',
            'academic_attendance_manage',
            
            // Reportes
            'academic_reports_view',
            
            // Inscripciones (solo lectura)
            'academic_enrollments_view',
        ];

        foreach ($academicPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // ========================================== //
        // 3. CREAR PERMISOS ADMINISTRATIVOS         //
        // ========================================== //
        $adminPermissions = [
            // Usuarios
            'admin_users_view',
            'admin_users_create',
            'admin_users_edit',
            'admin_users_delete',
            
            // Roles
            'admin_roles_view',
            'admin_roles_create',
            'admin_roles_edit',
            'admin_roles_delete',
            
            // Configuraciones
            'admin_settings_view',
            'admin_settings_edit',
            
            // Auditoría
            'admin_audit_view',
            
            // Mantenimiento
            'admin_maintenance_manage',
        ];

        foreach ($adminPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // ========================================== //
        // 4. CREAR PERMISOS DE CAJA (CASHIER)       //
        // ========================================== //
        $cashierPermissions = [
            // Pagos
            'cashier_payments_view',
            'cashier_payments_create',
            'cashier_payments_edit',
            'cashier_payments_delete',
            
            // Facturación
            'cashier_invoices_view',
            'cashier_invoices_create',
            'cashier_invoices_edit',
            
            // Reportes financieros
            'cashier_financial_reports_view',
            
            // Estudiantes (solo lectura)
            'cashier_students_view',
            
            // Inscripciones (solo lectura)
            'cashier_enrollments_view',
        ];

        foreach ($cashierPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // ========================================== //
        // 5. CREAR PERMISOS DE INSCRIPCIONES        //
        // ========================================== //
        $enrollerPermissions = [
            // Inscripciones
            'enroller_enrollments_view',
            'enroller_enrollments_create',
            'enroller_enrollments_edit',
            'enroller_enrollments_delete',
            
            // Matrículas
            'enroller_matriculas_view',
            'enroller_matriculas_create',
            'enroller_matriculas_edit',
            
            // Estudiantes
            'enroller_students_view',
            'enroller_students_create',
            'enroller_students_edit',
            
            // Cursos (solo lectura)
            'enroller_courses_view',
            
            // Reportes de inscripciones
            'enroller_enrollment_reports_view',
        ];

        foreach ($enrollerPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // ========================================== //
        // 6. CREAR PERMISOS DE ESTUDIANTES          //
        // ========================================== //
        $studentPermissions = [
            // Dashboard estudiante
            'student_dashboard_view',
            
            // Calificaciones (solo ver propias)
            'student_grades_view',
            
            // Asistencia (solo ver propia)
            'student_attendance_view',
            
            // Horario
            'student_schedule_view',
            
            // Perfil
            'student_profile_view',
            'student_profile_edit',
        ];

        foreach ($studentPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // ========================================== //
        // 7. ASIGNAR PERMISOS A ROLES              //
        // ========================================== //

        // 📚 Académico
        $academicoRole = Role::firstOrCreate(['name' => 'academico', 'guard_name' => 'web']);
        $academicoRole->syncPermissions($academicPermissions);

        // 👨‍💼 Admin
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($adminPermissions);

        // 💰 Cashier
        $cashierRole = Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
        $cashierRole->syncPermissions($cashierPermissions);

        // 📋 Enroller
        $enrollerRole = Role::firstOrCreate(['name' => 'enroller', 'guard_name' => 'web']);
        $enrollerRole->syncPermissions($enrollerPermissions);

        // 👨‍🎓 Student
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $studentRole->syncPermissions($studentPermissions);

        // 👑 Super-Admin (todos los permisos)
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // ========================================== //
        // 8. MENSAJE FINAL                         //
        // ========================================== //
        $this->command->info('✅ Permisos creados correctamente');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('📋 Total de permisos académicos: ' . count($academicPermissions));
        $this->command->info('📋 Total de permisos admin: ' . count($adminPermissions));
        $this->command->info('📋 Total de permisos cashier: ' . count($cashierPermissions));
        $this->command->info('📋 Total de permisos enroller: ' . count($enrollerPermissions));
        $this->command->info('📋 Total de permisos student: ' . count($studentPermissions));
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('👤 Roles con permisos asignados:');
        $this->command->info('   📚 academico  →  ' . count($academicPermissions) . ' permisos');
        $this->command->info('   👨‍💼 admin      →  ' . count($adminPermissions) . ' permisos');
        $this->command->info('   💰 cashier    →  ' . count($cashierPermissions) . ' permisos');
        $this->command->info('   📋 enroller   →  ' . count($enrollerPermissions) . ' permisos');
        $this->command->info('   👨‍🎓 student    →  ' . count($studentPermissions) . ' permisos');
        $this->command->info('   👑 super-admin →  TODOS los permisos');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}