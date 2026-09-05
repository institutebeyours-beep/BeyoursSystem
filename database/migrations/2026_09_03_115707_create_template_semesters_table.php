<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('template_semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_type_id')
                  ->constrained('template_types')
                  ->onDelete('cascade');
            $table->integer('semester_number');
            $table->integer('total_hours')->default(0);
            $table->integer('total_credits')->default(0);
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->unique(['template_type_id', 'semester_number']);
            $table->index(['template_type_id', 'order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('template_semesters');
    }
};