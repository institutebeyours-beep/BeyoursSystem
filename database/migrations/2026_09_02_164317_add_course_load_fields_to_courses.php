<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            // ✅ Tipo de curso
            $table->enum('course_type', ['theoretical', 'theoretical_practical', 'practical', 'specialized_lab'])
                  ->default('theoretical_practical')
                  ->after('description');
            
            // ✅ Cargas calculadas
            $table->decimal('class_hours_per_week', 5, 2)->default(0)->after('course_type');
            $table->decimal('study_hours_per_week', 5, 2)->default(0)->after('class_hours_per_week');
            $table->decimal('lab_hours_per_week', 5, 2)->default(0)->after('study_hours_per_week');
            $table->decimal('total_hours_per_week', 5, 2)->default(0)->after('lab_hours_per_week');
            $table->integer('total_weeks')->default(16)->after('total_hours_per_week');
            $table->decimal('total_hours', 8, 2)->default(0)->after('total_weeks');
            
            // ✅ Proporciones (para referencia)
            $table->decimal('study_ratio', 3, 1)->default(2.0)->after('total_hours');
            $table->decimal('lab_ratio', 3, 1)->default(0.5)->after('study_ratio');
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'course_type',
                'class_hours_per_week',
                'study_hours_per_week',
                'lab_hours_per_week',
                'total_hours_per_week',
                'total_weeks',
                'total_hours',
                'study_ratio',
                'lab_ratio'
            ]);
        });
    }
};