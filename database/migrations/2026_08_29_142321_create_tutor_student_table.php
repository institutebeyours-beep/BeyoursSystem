<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tutor_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->unique(['tutor_id', 'student_id']);
            $table->index(['status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tutor_student');
    }
};