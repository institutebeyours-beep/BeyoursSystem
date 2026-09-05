<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ✅ Verificar si la columna NO existe
        if (!Schema::hasColumn('grade_configurations', 'subject_id')) {
            Schema::table('grade_configurations', function (Blueprint $table) {
                $table->foreignId('subject_id')
                      ->nullable()
                      ->after('course_id')
                      ->constrained('subjects')
                      ->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('grade_configurations', 'subject_id')) {
            Schema::table('grade_configurations', function (Blueprint $table) {
                $table->dropForeign(['subject_id']);
                $table->dropColumn('subject_id');
            });
        }
    }
};