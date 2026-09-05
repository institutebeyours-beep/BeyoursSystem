<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            // ✅ 1. Roles y Permisos (SIEMPRE PRIMERO)
            RolesAndPermissionsSeeder::class,

            // ✅ 2. Configuraciones
            SettingsSeeder::class,
            AddSidebarColorsSeeder::class,

            // ✅ 3. Usuarios
            UserSeeder::class,

            // ✅ 4. Tipos de Enseñanza
            EducationTypesSeeder::class,

            // ✅ 5. Plantillas
            TemplateTypesSeeder::class,
            TemplateSemestersSeeder::class,
            TemplateSubjectsSeeder::class,

            // ✅ 6. Carreras
            CareersSeeder::class,

            // ✅ 7. Cursos
            CoursesSeeder::class,

            // ✅ 8. Asignaturas
            SubjectsSeeder::class,

            // ✅ 9. Permisos Académicos
            AcademicPermissionsSeeder::class,

            // ✅ 10. Seeder Completo (opcional)
            CompleteSeeder::class,
        ]);
    }
}