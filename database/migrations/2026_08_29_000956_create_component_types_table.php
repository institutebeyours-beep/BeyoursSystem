<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('component_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('icon', 50)->default('📌');
            $table->string('color', 50)->default('gray');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insertar tipos por defecto
        DB::table('component_types')->insert([
            [
                'name' => 'Parcial',
                'slug' => 'partial',
                'icon' => '📝',
                'color' => 'blue',
                'description' => 'Evaluaciones parciales durante el curso',
                'is_default' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Examen Final',
                'slug' => 'final',
                'icon' => '📊',
                'color' => 'purple',
                'description' => 'Examen final del curso',
                'is_default' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Asistencia',
                'slug' => 'attendance',
                'icon' => '📋',
                'color' => 'green',
                'description' => 'Registro de asistencia',
                'is_default' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Proyecto',
                'slug' => 'project',
                'icon' => '🚀',
                'color' => 'orange',
                'description' => 'Proyectos especiales o finales',
                'is_default' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'Tarea',
                'slug' => 'homework',
                'icon' => '📚',
                'color' => 'yellow',
                'description' => 'Tareas y trabajos prácticos',
                'is_default' => true,
                'sort_order' => 5
            ],
            [
                'name' => 'Quiz',
                'slug' => 'quiz',
                'icon' => '🧪',
                'color' => 'red',
                'description' => 'Pruebas cortas o quices',
                'is_default' => true,
                'sort_order' => 6
            ],
            [
                'name' => 'Otro',
                'slug' => 'other',
                'icon' => '📌',
                'color' => 'gray',
                'description' => 'Otros tipos de evaluación',
                'is_default' => true,
                'sort_order' => 7
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('component_types');
    }
};