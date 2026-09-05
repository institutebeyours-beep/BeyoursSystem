<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TemplateType;
use App\Models\TemplateSemester;

class TemplateSemestersSeeder extends Seeder
{
    public function run()
    {
        // Obtener plantillas
        $idiomas = TemplateType::where('code', 'TS-IDIOMAS')->first();
        $informatica = TemplateType::where('code', 'TS-INFO')->first();
        $bachillerato = TemplateType::where('code', 'BACH-C')->first();
        $tecMedio = TemplateType::where('code', 'TM-INFO')->first();

        // ========================================== //
        // Técnico Superior en Idiomas (6 semestres)
        // ========================================== //
        if ($idiomas) {
            $semesters = [
                ['semester_number' => 1, 'total_hours' => 450, 'total_credits' => 24, 'order' => 0],
                ['semester_number' => 2, 'total_hours' => 450, 'total_credits' => 24, 'order' => 1],
                ['semester_number' => 3, 'total_hours' => 450, 'total_credits' => 24, 'order' => 2],
                ['semester_number' => 4, 'total_hours' => 450, 'total_credits' => 24, 'order' => 3],
                ['semester_number' => 5, 'total_hours' => 450, 'total_credits' => 24, 'order' => 4],
                ['semester_number' => 6, 'total_hours' => 450, 'total_credits' => 24, 'order' => 5],
            ];

            foreach ($semesters as $semester) {
                TemplateSemester::firstOrCreate([
                    'template_type_id' => $idiomas->id,
                    'semester_number' => $semester['semester_number'],
                ], $semester);
            }
        }

        // ========================================== //
        // Técnico Superior en Informática (6 semestres)
        // ========================================== //
        if ($informatica) {
            $semesters = [
                ['semester_number' => 1, 'total_hours' => 450, 'total_credits' => 24, 'order' => 0],
                ['semester_number' => 2, 'total_hours' => 450, 'total_credits' => 24, 'order' => 1],
                ['semester_number' => 3, 'total_hours' => 450, 'total_credits' => 24, 'order' => 2],
                ['semester_number' => 4, 'total_hours' => 450, 'total_credits' => 24, 'order' => 3],
                ['semester_number' => 5, 'total_hours' => 450, 'total_credits' => 24, 'order' => 4],
                ['semester_number' => 6, 'total_hours' => 450, 'total_credits' => 24, 'order' => 5],
            ];

            foreach ($semesters as $semester) {
                TemplateSemester::firstOrCreate([
                    'template_type_id' => $informatica->id,
                    'semester_number' => $semester['semester_number'],
                ], $semester);
            }
        }

        // ========================================== //
        // Bachillerato en Ciencias (4 semestres)
        // ========================================== //
        if ($bachillerato) {
            $semesters = [
                ['semester_number' => 1, 'total_hours' => 500, 'total_credits' => 30, 'order' => 0],
                ['semester_number' => 2, 'total_hours' => 500, 'total_credits' => 30, 'order' => 1],
                ['semester_number' => 3, 'total_hours' => 500, 'total_credits' => 30, 'order' => 2],
                ['semester_number' => 4, 'total_hours' => 500, 'total_credits' => 30, 'order' => 3],
            ];

            foreach ($semesters as $semester) {
                TemplateSemester::firstOrCreate([
                    'template_type_id' => $bachillerato->id,
                    'semester_number' => $semester['semester_number'],
                ], $semester);
            }
        }

        // ========================================== //
        // Técnico Medio en Informática (4 semestres)
        // ========================================== //
        if ($tecMedio) {
            $semesters = [
                ['semester_number' => 1, 'total_hours' => 450, 'total_credits' => 24, 'order' => 0],
                ['semester_number' => 2, 'total_hours' => 450, 'total_credits' => 24, 'order' => 1],
                ['semester_number' => 3, 'total_hours' => 450, 'total_credits' => 24, 'order' => 2],
                ['semester_number' => 4, 'total_hours' => 450, 'total_credits' => 24, 'order' => 3],
            ];

            foreach ($semesters as $semester) {
                TemplateSemester::firstOrCreate([
                    'template_type_id' => $tecMedio->id,
                    'semester_number' => $semester['semester_number'],
                ], $semester);
            }
        }
    }
}