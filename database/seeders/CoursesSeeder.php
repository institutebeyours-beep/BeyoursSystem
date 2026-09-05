<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Career;

class CoursesSeeder extends Seeder
{
    public function run()
    {
        // Obtener carreras
        $bachC = Career::where('code', 'BACH-C')->first();
        $bachL = Career::where('code', 'BACH-L')->first();
        $idiomas = Career::where('code', 'TS-IDIOMAS')->first();
        $info = Career::where('code', 'TS-INFO')->first();
        $tmInfo = Career::where('code', 'TM-INFO')->first();

        $courses = [
            // Bachillerato en Ciencias
            [
                'career_id' => $bachC?->id,
                'name' => '1º Bachillerato',
                'code' => 'BACH-C-1',
                'description' => 'Primer año de Bachillerato en Ciencias',
                'total_credits' => 30,
                'duration' => 0,
                'capacity' => 30,
                'status' => 'active',
            ],
            [
                'career_id' => $bachC?->id,
                'name' => '2º Bachillerato',
                'code' => 'BACH-C-2',
                'description' => 'Segundo año de Bachillerato en Ciencias',
                'total_credits' => 30,
                'duration' => 0,
                'capacity' => 30,
                'status' => 'active',
            ],
            
            // Bachillerato en Letras
            [
                'career_id' => $bachL?->id,
                'name' => '1º Bachillerato',
                'code' => 'BACH-L-1',
                'description' => 'Primer año de Bachillerato en Letras',
                'total_credits' => 30,
                'duration' => 0,
                'capacity' => 30,
                'status' => 'active',
            ],
            [
                'career_id' => $bachL?->id,
                'name' => '2º Bachillerato',
                'code' => 'BACH-L-2',
                'description' => 'Segundo año de Bachillerato en Letras',
                'total_credits' => 30,
                'duration' => 0,
                'capacity' => 30,
                'status' => 'active',
            ],
            
            // Técnico Superior en Idiomas
            [
                'career_id' => $idiomas?->id,
                'name' => '1º Curso - Idiomas',
                'code' => 'TS-IDIOMAS-1',
                'description' => 'Primer año de Técnico Superior en Idiomas',
                'total_credits' => 48,
                'duration' => 0,
                'capacity' => 25,
                'status' => 'active',
            ],
            [
                'career_id' => $idiomas?->id,
                'name' => '2º Curso - Idiomas',
                'code' => 'TS-IDIOMAS-2',
                'description' => 'Segundo año de Técnico Superior en Idiomas',
                'total_credits' => 48,
                'duration' => 0,
                'capacity' => 25,
                'status' => 'active',
            ],
            [
                'career_id' => $idiomas?->id,
                'name' => '3º Curso - Idiomas',
                'code' => 'TS-IDIOMAS-3',
                'description' => 'Tercer año de Técnico Superior en Idiomas',
                'total_credits' => 48,
                'duration' => 0,
                'capacity' => 25,
                'status' => 'active',
            ],
            
            // Técnico Superior en Informática
            [
                'career_id' => $info?->id,
                'name' => '1º Curso - Informática',
                'code' => 'TS-INFO-1',
                'description' => 'Primer año de Técnico Superior en Informática',
                'total_credits' => 48,
                'duration' => 0,
                'capacity' => 25,
                'status' => 'active',
            ],
            [
                'career_id' => $info?->id,
                'name' => '2º Curso - Informática',
                'code' => 'TS-INFO-2',
                'description' => 'Segundo año de Técnico Superior en Informática',
                'total_credits' => 48,
                'duration' => 0,
                'capacity' => 25,
                'status' => 'active',
            ],
            [
                'career_id' => $info?->id,
                'name' => '3º Curso - Informática',
                'code' => 'TS-INFO-3',
                'description' => 'Tercer año de Técnico Superior en Informática',
                'total_credits' => 48,
                'duration' => 0,
                'capacity' => 25,
                'status' => 'active',
            ],
            
            // Técnico Medio en Informática
            [
                'career_id' => $tmInfo?->id,
                'name' => '1º Curso - TM Informática',
                'code' => 'TM-INFO-1',
                'description' => 'Primer año de Técnico Medio en Informática',
                'total_credits' => 48,
                'duration' => 0,
                'capacity' => 25,
                'status' => 'active',
            ],
            [
                'career_id' => $tmInfo?->id,
                'name' => '2º Curso - TM Informática',
                'code' => 'TM-INFO-2',
                'description' => 'Segundo año de Técnico Medio en Informática',
                'total_credits' => 48,
                'duration' => 0,
                'capacity' => 25,
                'status' => 'active',
            ],
        ];

        foreach ($courses as $course) {
            Course::firstOrCreate(
                ['code' => $course['code']],
                $course
            );
        }
    }
}