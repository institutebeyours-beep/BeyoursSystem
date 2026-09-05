<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectsSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // ========================================== //
            // Matemáticas
            // ========================================== //
            ['name' => 'Matemáticas I', 'code' => 'MAT101', 'credits' => 6, 'theoretical_hours' => 4, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Matemáticas II', 'code' => 'MAT102', 'credits' => 6, 'theoretical_hours' => 4, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Matemáticas III', 'code' => 'MAT201', 'credits' => 6, 'theoretical_hours' => 4, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Matemáticas IV', 'code' => 'MAT202', 'credits' => 6, 'theoretical_hours' => 4, 'practical_hours' => 2, 'is_active' => true],
            
            // ========================================== //
            // Lengua y Literatura
            // ========================================== //
            ['name' => 'Lengua y Literatura', 'code' => 'LEN101', 'credits' => 4, 'theoretical_hours' => 4, 'practical_hours' => 0, 'is_active' => true],
            ['name' => 'Lengua y Literatura II', 'code' => 'LEN102', 'credits' => 4, 'theoretical_hours' => 4, 'practical_hours' => 0, 'is_active' => true],
            
            // ========================================== //
            // Historia
            // ========================================== //
            ['name' => 'Historia', 'code' => 'HIS101', 'credits' => 4, 'theoretical_hours' => 4, 'practical_hours' => 0, 'is_active' => true],
            ['name' => 'Historia II', 'code' => 'HIS102', 'credits' => 4, 'theoretical_hours' => 4, 'practical_hours' => 0, 'is_active' => true],
            ['name' => 'Historia III', 'code' => 'HIS201', 'credits' => 4, 'theoretical_hours' => 4, 'practical_hours' => 0, 'is_active' => true],
            
            // ========================================== //
            // Ciencias
            // ========================================== //
            ['name' => 'Química I', 'code' => 'QUI101', 'credits' => 5, 'theoretical_hours' => 3, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Química II', 'code' => 'QUI102', 'credits' => 5, 'theoretical_hours' => 3, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Química III', 'code' => 'QUI201', 'credits' => 5, 'theoretical_hours' => 3, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Química IV', 'code' => 'QUI202', 'credits' => 5, 'theoretical_hours' => 3, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Biología I', 'code' => 'BIO101', 'credits' => 5, 'theoretical_hours' => 3, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Biología II', 'code' => 'BIO102', 'credits' => 5, 'theoretical_hours' => 3, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Biología III', 'code' => 'BIO201', 'credits' => 5, 'theoretical_hours' => 3, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Biología IV', 'code' => 'BIO202', 'credits' => 5, 'theoretical_hours' => 3, 'practical_hours' => 2, 'is_active' => true],
            
            // ========================================== //
            // Física
            // ========================================== //
            ['name' => 'Física I', 'code' => 'FIS101', 'credits' => 5, 'theoretical_hours' => 3, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Física II', 'code' => 'FIS102', 'credits' => 5, 'theoretical_hours' => 3, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Física III', 'code' => 'FIS201', 'credits' => 5, 'theoretical_hours' => 3, 'practical_hours' => 2, 'is_active' => true],
            
            // ========================================== //
            // Idiomas (Técnico Superior)
            // ========================================== //
            ['name' => 'Fundamentos de Gramática', 'code' => 'GRA101', 'credits' => 4, 'theoretical_hours' => 3, 'practical_hours' => 1, 'is_active' => true],
            ['name' => 'Fonética y Pronunciación', 'code' => 'FON101', 'credits' => 4, 'theoretical_hours' => 2, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Comprensión Lectora', 'code' => 'COM101', 'credits' => 4, 'theoretical_hours' => 2, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Comunicación Oral Básica', 'code' => 'ORL101', 'credits' => 4, 'theoretical_hours' => 2, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Cultura Anglófona', 'code' => 'CUL101', 'credits' => 4, 'theoretical_hours' => 3, 'practical_hours' => 1, 'is_active' => true],
            ['name' => 'Laboratorio de Idiomas I', 'code' => 'LAB101', 'credits' => 4, 'theoretical_hours' => 1, 'practical_hours' => 3, 'is_active' => true],
            
            // ========================================== //
            // Informática (Técnico Superior y Medio)
            // ========================================== //
            ['name' => 'Fundamentos de Informática', 'code' => 'INF101', 'credits' => 4, 'theoretical_hours' => 2, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Lógica de Programación', 'code' => 'PRO101', 'credits' => 5, 'theoretical_hours' => 2, 'practical_hours' => 3, 'is_active' => true],
            ['name' => 'Diseño Web Básico', 'code' => 'WEB101', 'credits' => 4, 'theoretical_hours' => 2, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Programación I', 'code' => 'PRO201', 'credits' => 5, 'theoretical_hours' => 2, 'practical_hours' => 3, 'is_active' => true],
            ['name' => 'Base de Datos I', 'code' => 'BDA101', 'credits' => 5, 'theoretical_hours' => 3, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Redes I', 'code' => 'RED101', 'credits' => 4, 'theoretical_hours' => 2, 'practical_hours' => 2, 'is_active' => true],
            
            // ========================================== //
            // Educación Física, Filosofía, etc.
            // ========================================== //
            ['name' => 'Educación Física', 'code' => 'EDF101', 'credits' => 2, 'theoretical_hours' => 0, 'practical_hours' => 2, 'is_active' => true],
            ['name' => 'Filosofía', 'code' => 'FIL101', 'credits' => 3, 'theoretical_hours' => 3, 'practical_hours' => 0, 'is_active' => true],
            ['name' => 'Geografía', 'code' => 'GEO101', 'credits' => 3, 'theoretical_hours' => 3, 'practical_hours' => 0, 'is_active' => true],
            ['name' => 'Educación Cívica', 'code' => 'CIV101', 'credits' => 2, 'theoretical_hours' => 2, 'practical_hours' => 0, 'is_active' => true],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(
                ['code' => $subject['code']],
                $subject
            );
        }
    }
}