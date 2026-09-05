<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('teacher_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('year')->nullable();
            $table->integer('semester')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->unique(['teacher_id', 'course_id', 'year', 'semester']);
            $table->index(['status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('teacher_course');
    }
};