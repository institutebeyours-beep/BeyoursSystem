<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EducationType;

class EducationTypesSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => 'Bachillerato', 'code' => 'BACH', 'description' => 'Educación secundaria superior', 'sort_order' => 1],
            ['name' => 'Técnico Superior', 'code' => 'TS', 'description' => 'Formación profesional de nivel superior', 'sort_order' => 2],
            ['name' => 'Técnico Medio', 'code' => 'TM', 'description' => 'Formación profesional de nivel medio', 'sort_order' => 3],
            ['name' => 'Capacitación', 'code' => 'CAP', 'description' => 'Cursos de capacitación y actualización', 'sort_order' => 4],
        ];

        foreach ($types as $type) {
            EducationType::firstOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}