<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('template_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_semester_id')
                  ->constrained('template_semesters')
                  ->onDelete('cascade');
            $table->string('name', 100);
            $table->string('code', 20)->nullable();
            $table->integer('credits')->default(0);
            $table->integer('theoretical_hours')->default(0);
            $table->integer('practical_hours')->default(0);
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['template_semester_id', 'order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('template_subjects');
    }
};