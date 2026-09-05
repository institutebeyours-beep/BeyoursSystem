<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_careers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->onDelete('cascade');
            $table->foreignId('career_id')
                  ->constrained('careers')
                  ->onDelete('cascade');
            $table->date('enrollment_date');
            $table->enum('status', ['active', 'inactive', 'completed', 'suspended'])
                  ->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'career_id']);
            $table->index(['student_id', 'status']);
            $table->index(['career_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_careers');
    }
};