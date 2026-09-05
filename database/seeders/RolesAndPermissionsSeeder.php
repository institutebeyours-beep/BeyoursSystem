<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // ========================================== //
        // 1. PERMISOS
        // ========================================== //
        $permissions = [
            // ===== USUARIOS =====
            'view_users', 'create_users', 'edit_users', 'delete_users',
            
            // ===== ROLES =====
            'view_roles', 'create_roles', 'edit_roles', 'delete_roles',
            
            // ===== REPORTES GENERALES =====
            'view_reports',
            
            // ===== DASHBOARD =====
            'academic_dashboard_view',
            
            // ===== TIPOS DE ENSEÑANZA (SOLO ADMIN) =====
            'education_types_view', 'education_types_create', 'education_types_edit', 'education_types_delete',
            
            // ===== CARRERAS =====
            'academic_careers_view', 
            'academic_careers_create', 
            'academic_careers_edit', 
            'academic_careers_delete',
            
            // ===== PLANTILLAS =====
            'academic_templates_view', 
            'academic_templates_create', 
            'academic_templates_edit', 
            'academic_templates_delete',
            
            // ===== CURSOS =====
            'academic_courses_view', 
            'academic_courses_create', 
            'academic_courses_edit', 
            'academic_courses_delete',
            
            // ===== ASIGNATURAS =====
            'academic_subjects_view', 
            'academic_subjects_create', 
            'academic_subjects_edit', 
            'academic_subjects_delete',
            
            // ===== ESTUDIANTES =====
            'academic_students_view', 
            'academic_students_create', 
            'academic_students_edit', 
            'academic_students_delete',
            
            // ===== CALIFICACIONES =====
            'academic_grades_view', 
            'academic_grades_manage',
            
            // ===== ASISTENCIA =====
            'academic_attendance_view', 
            'academic_attendance_manage',
            
            // ===== REPORTES ACADÉMICOS =====
            'academic_reports_view',
            
            // ===== INSCRIPCIONES =====
            'academic_enrollments_view',
            
            // ===== TIPOS DE COMPONENTE =====
            'academic_component_types_view', 
            'academic_component_types_manage',
            
            // ===== TIPOS DE ENSEÑANZA (público) =====
            'education_types_public',
        ];

        // ✅ Crear todos los permisos
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // ========================================== //
        // 2. ROLES Y ASIGNACIÓN DE PERMISOS
        // ========================================== //

        // ===== 2.1. SUPER-ADMIN (TODOS LOS PERMISOS) =====
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // ===== 2.2. ADMIN (PERMISOS ADMINISTRATIVOS + ACADÉMICOS) =====
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            // Administración
            'view_users', 'create_users', 'edit_users',
            'view_roles', 'create_roles', 'edit_roles',
            'education_types_view', 'education_types_create', 'education_types_edit', 'education_types_delete',
            'view_reports',
            
            // Académico (todos)
            'academic_dashboard_view',
            'academic_careers_view', 'academic_careers_create', 'academic_careers_edit', 'academic_careers_delete',
            'academic_templates_view', 'academic_templates_create', 'academic_templates_edit', 'academic_templates_delete',
            'academic_courses_view', 'academic_courses_create', 'academic_courses_edit', 'academic_courses_delete',
            'academic_subjects_view', 'academic_subjects_create', 'academic_subjects_edit', 'academic_subjects_delete',
            'academic_students_view', 'academic_students_create', 'academic_students_edit', 'academic_students_delete',
            'academic_grades_view', 'academic_grades_manage',
            'academic_attendance_view', 'academic_attendance_manage',
            'academic_reports_view',
            'academic_enrollments_view',
            'academic_component_types_view', 'academic_component_types_manage',
        ]);

        // ===== 2.3. ACADÉMICO (TODOS LOS PERMISOS ACADÉMICOS - SIN ELIMINAR CARRERAS) =====
        $academico = Role::firstOrCreate(['name' => 'academico', 'guard_name' => 'web']);
        $academico->syncPermissions([
            // Dashboard
            'academic_dashboard_view',
            
            // Carreras (ver, crear, editar - NO eliminar)
            'academic_careers_view', 
            'academic_careers_create', 
            'academic_careers_edit',
            
            // Plantillas (ver, crear, editar, eliminar sus propias)
            'academic_templates_view', 
            'academic_templates_create', 
            'academic_templates_edit', 
            'academic_templates_delete',
            
            // Cursos (todos)
            'academic_courses_view', 
            'academic_courses_create', 
            'academic_courses_edit', 
            'academic_courses_delete',
            
            // Asignaturas (todos)
            'academic_subjects_view', 
            'academic_subjects_create', 
            'academic_subjects_edit', 
            'academic_subjects_delete',
            
            // Estudiantes (todos)
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
            
            // Inscripciones
            'academic_enrollments_view',
            
            // Tipos de Componente
            'academic_component_types_view', 
            'academic_component_types_manage',
        ]);

        // ===== 2.4. DOCENTE (PERMISOS BÁSICOS) =====
        $docente = Role::firstOrCreate(['name' => 'docente', 'guard_name' => 'web']);
        $docente->syncPermissions([
            'academic_dashboard_view',
            'academic_courses_view',
            'academic_students_view',
            'academic_grades_view', 
            'academic_grades_manage',
            'academic_attendance_view', 
            'academic_attendance_manage',
            'academic_reports_view',
        ]);

        // ===== 2.5. ESTUDIANTE (SOLO LECTURA) =====
        $estudiante = Role::firstOrCreate(['name' => 'estudiante', 'guard_name' => 'web']);
        $estudiante->syncPermissions([
            'academic_courses_view',
            'academic_students_view',
            'academic_grades_view',
        ]);

        // ========================================== //
        // 3. VERIFICACIÓN
        // ========================================== //
        $this->command->info('✅ Roles y permisos creados/actualizados exitosamente');
        $this->command->info('📋 Permisos de carreras:');
        $this->command->info('   - academic_careers_view');
        $this->command->info('   - academic_careers_create');
        $this->command->info('   - academic_careers_edit');
        $this->command->info('   - academic_careers_delete');
        $this->command->info('👤 Rol académico tiene: view, create, edit (sin delete)');
    }
}