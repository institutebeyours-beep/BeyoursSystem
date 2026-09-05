<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TemplateSemester;
use App\Models\TemplateSubject;

class TemplateSubjectsSeeder extends Seeder
{
    public function run()
    {
        // ========================================== //
        // Técnico Superior en Idiomas
        // ========================================== //
        $semesters = TemplateSemester::whereHas('templateType', function ($q) {
            $q->where('code', 'TS-IDIOMAS');
        })->get();

        $subjectsBySemester = [
            1 => [
                'Fundamentos de Gramática', 'Fonética y Pronunciación',
                'Comprensión Lectora', 'Comunicación Oral Básica',
                'Cultura Anglófona', 'Laboratorio de Idiomas I'
            ],
            2 => [
                'Conversación Intermedia', 'Escritura Académica',
                'Cultura Anglófona II', 'Laboratorio de Idiomas II',
                'Traducción Básica', 'Comprensión Auditiva'
            ],
            3 => [
                'Conversación Avanzada', 'Escritura Creativa',
                'Literatura Breve', 'Práctica de Interpretación',
                'Didáctica de Idiomas', 'Laboratorio de Idiomas III'
            ],
            4 => [
                'Metodología de Enseñanza', 'Traducción Literaria',
                'Lingüística Aplicada', 'Práctica Docente I',
                'Seminario de Investigación', 'Electiva I'
            ],
            5 => [
                'Didáctica Aplicada', 'Traducción Técnica',
                'Proyectos de Traducción', 'Práctica Pre-Profesional I',
                'Práctica Docente II', 'Electiva II'
            ],
            6 => [
                'Seminario de Investigación', 'Práctica Laboral Supervisada',
                'Proyecto de Grado', 'Práctica Pre-Profesional II',
                'Gestión Educativa', 'Electiva III'
            ],
        ];

        foreach ($semesters as $semester) {
            $subjects = $subjectsBySemester[$semester->semester_number] ?? [];
            
            foreach ($subjects as $index => $name) {
                TemplateSubject::firstOrCreate([
                    'template_semester_id' => $semester->id,
                    'name' => $name,
                ], [
                    'credits' => 4,
                    'theoretical_hours' => 3,    // ✅ AGREGADO
                    'practical_hours' => 1,      // ✅ AGREGADO
                    'order' => $index,
                    'description' => null,
                ]);
            }
        }

        // ========================================== //
        // Bachillerato en Ciencias
        // ========================================== //
        $bachilleratoSemesters = TemplateSemester::whereHas('templateType', function ($q) {
            $q->where('code', 'BACH-C');
        })->get();

        $bachilleratoSubjects = [
            1 => ['Matemáticas I', 'Lengua y Literatura', 'Historia', 'Química I', 'Biología I', 'Educación Física'],
            2 => ['Matemáticas II', 'Lengua y Literatura II', 'Historia II', 'Química II', 'Biología II', 'Física I'],
            3 => ['Matemáticas III', 'Filosofía', 'Geografía', 'Física II', 'Química III', 'Biología III'],
            4 => ['Matemáticas IV', 'Educación Cívica', 'Historia III', 'Física III', 'Química IV', 'Biología IV'],
        ];

        foreach ($bachilleratoSemesters as $semester) {
            $subjects = $bachilleratoSubjects[$semester->semester_number] ?? [];
            
            foreach ($subjects as $index => $name) {
                TemplateSubject::firstOrCreate([
                    'template_semester_id' => $semester->id,
                    'name' => $name,
                ], [
                    'credits' => 5,
                    'theoretical_hours' => 4,    // ✅ AGREGADO
                    'practical_hours' => 1,      // ✅ AGREGADO
                    'order' => $index,
                    'description' => null,
                ]);
            }
        }

        // ========================================== //
        // Técnico Superior en Informática
        // ========================================== //
        $infoSemesters = TemplateSemester::whereHas('templateType', function ($q) {
            $q->where('code', 'TS-INFO');
        })->get();

        $infoSubjects = [
            1 => ['Fundamentos de Informática', 'Matemáticas Aplicadas', 'Lógica de Programación', 'Diseño Web Básico', 'Electrónica Básica', 'Taller de Reparación'],
            2 => ['Programación I', 'Base de Datos I', 'Diseño Gráfico', 'Redes I', 'Sistemas Operativos', 'Taller de Montaje'],
            3 => ['Programación II', 'Base de Datos II', 'Desarrollo Web', 'Redes II', 'Seguridad Informática', 'Taller de Proyectos I'],
            4 => ['Programación III', 'Administración de Servidores', 'Desarrollo de Aplicaciones', 'Proyecto Integrador', 'Práctica Profesional', 'Taller de Proyectos II'],
            5 => ['Programación IV', 'Inteligencia Artificial', 'Análisis de Datos', 'Gestión de Proyectos', 'Electiva I', 'Taller de Proyectos III'],
            6 => ['Seminario de Investigación', 'Práctica Laboral Supervisada', 'Proyecto de Grado', 'Gestión de TI', 'Electiva II', 'Taller de Proyectos IV'],
        ];

        foreach ($infoSemesters as $semester) {
            $subjects = $infoSubjects[$semester->semester_number] ?? [];
            
            foreach ($subjects as $index => $name) {
                TemplateSubject::firstOrCreate([
                    'template_semester_id' => $semester->id,
                    'name' => $name,
                ], [
                    'credits' => 4,
                    'theoretical_hours' => 2,    // ✅ AGREGADO
                    'practical_hours' => 2,      // ✅ AGREGADO (más práctico)
                    'order' => $index,
                    'description' => null,
                ]);
            }
        }

        // ========================================== //
        // Técnico Medio en Informática
        // ========================================== //
        $tmInfoSemesters = TemplateSemester::whereHas('templateType', function ($q) {
            $q->where('code', 'TM-INFO');
        })->get();

        $tmInfoSubjects = [
            1 => ['Fundamentos de Informática', 'Matemáticas Aplicadas', 'Lógica de Programación', 'Diseño Web Básico', 'Electrónica Básica', 'Taller de Reparación'],
            2 => ['Programación I', 'Base de Datos I', 'Diseño Gráfico', 'Redes I', 'Sistemas Operativos', 'Taller de Montaje'],
            3 => ['Programación II', 'Base de Datos II', 'Desarrollo Web', 'Redes II', 'Seguridad Informática', 'Taller de Proyectos I'],
            4 => ['Programación III', 'Administración de Servidores', 'Desarrollo de Aplicaciones', 'Proyecto Integrador', 'Práctica Profesional', 'Taller de Proyectos II'],
        ];

        foreach ($tmInfoSemesters as $semester) {
            $subjects = $tmInfoSubjects[$semester->semester_number] ?? [];
            
            foreach ($subjects as $index => $name) {
                TemplateSubject::firstOrCreate([
                    'template_semester_id' => $semester->id,
                    'name' => $name,
                ], [
                    'credits' => 4,
                    'theoretical_hours' => 2,    // ✅ AGREGADO
                    'practical_hours' => 2,      // ✅ AGREGADO
                    'order' => $index,
                    'description' => null,
                ]);
            }
        }
    }
}