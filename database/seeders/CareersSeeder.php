<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Career;
use App\Models\EducationType;

class CareersSeeder extends Seeder
{
    public function run()
    {
        // Obtener tipos de enseñanza
        $bachillerato = EducationType::where('code', 'BACH')->first();
        $tecSuperior = EducationType::where('code', 'TS')->first();
        $tecMedio = EducationType::where('code', 'TM')->first();

        $careers = [
            [
                'education_type_id' => $bachillerato?->id,
                'name' => 'Bachillerato en Ciencias',
                'code' => 'BACH-C',
                'description' => 'Ciencias exactas y naturales',
                'total_credits' => 120,
                'theoretical_hours' => 800,
                'practical_hours' => 400,
                'duration_years' => 2,
                'duration_semesters' => 4,
                'is_active' => true,
            ],
            [
                'education_type_id' => $bachillerato?->id,
                'name' => 'Bachillerato en Letras',
                'code' => 'BACH-L',
                'description' => 'Humanidades y ciencias sociales',
                'total_credits' => 120,
                'theoretical_hours' => 800,
                'practical_hours' => 400,
                'duration_years' => 2,
                'duration_semesters' => 4,
                'is_active' => true,
            ],
            [
                'education_type_id' => $tecSuperior?->id,
                'name' => 'Técnico Superior en Idiomas',
                'code' => 'TS-IDIOMAS',
                'description' => 'Formación en idiomas extranjeros',
                'total_credits' => 144,
                'theoretical_hours' => 900,
                'practical_hours' => 600,
                'duration_years' => 3,
                'duration_semesters' => 6,
                'is_active' => true,
            ],
            [
                'education_type_id' => $tecSuperior?->id,
                'name' => 'Técnico Superior en Informática',
                'code' => 'TS-INFO',
                'description' => 'Tecnologías de la información',
                'total_credits' => 144,
                'theoretical_hours' => 900,
                'practical_hours' => 600,
                'duration_years' => 3,
                'duration_semesters' => 6,
                'is_active' => true,
            ],
            [
                'education_type_id' => $tecMedio?->id,
                'name' => 'Técnico Medio en Informática',
                'code' => 'TM-INFO',
                'description' => 'Informática básica y mantenimiento',
                'total_credits' => 96,
                'theoretical_hours' => 600,
                'practical_hours' => 400,
                'duration_years' => 2,
                'duration_semesters' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($careers as $career) {
            Career::firstOrCreate(
                ['code' => $career['code']],
                $career
            );
        }
    }
}