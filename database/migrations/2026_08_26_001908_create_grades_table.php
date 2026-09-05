<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('partial')->default(1); // 1, 2, 3
            $table->decimal('grade', 5, 2)->nullable();
            $table->decimal('grade_final', 5, 2)->nullable(); // Calificación final calculada
            $table->text('observations')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->unique(['student_id', 'course_id', 'partial']);
            $table->index(['course_id', 'partial']);
            $table->index('grade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('grades');
    }
};