<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TemplateType;
use App\Models\EducationType;

class TemplateTypesSeeder extends Seeder
{
    public function run()
    {
        // Obtener tipos de enseñanza
        $bachillerato = EducationType::where('code', 'BACH')->first();
        $tecSuperior = EducationType::where('code', 'TS')->first();
        $tecMedio = EducationType::where('code', 'TM')->first();

        $templates = [
            [
                'name' => 'Técnico Superior en Idiomas',
                'code' => 'TS-IDIOMAS',
                'description' => '3 años, 6 semestres, 24 créditos por semestre',
                'education_type_id' => $tecSuperior?->id,
                'is_default' => true,
            ],
            [
                'name' => 'Técnico Superior en Informática',
                'code' => 'TS-INFO',
                'description' => '3 años, 6 semestres, 24 créditos por semestre',
                'education_type_id' => $tecSuperior?->id,
                'is_default' => true,
            ],
            [
                'name' => 'Bachillerato en Ciencias',
                'code' => 'BACH-C',
                'description' => '2 años, 4 semestres, 30 créditos por semestre',
                'education_type_id' => $bachillerato?->id,
                'is_default' => true,
            ],
            [
                'name' => 'Técnico Medio en Informática',
                'code' => 'TM-INFO',
                'description' => '2 años, 4 semestres, 24 créditos por semestre',
                'education_type_id' => $tecMedio?->id,
                'is_default' => true,
            ],
        ];

        foreach ($templates as $template) {
            TemplateType::firstOrCreate(
                ['code' => $template['code']],
                $template
            );
        }
    }
}