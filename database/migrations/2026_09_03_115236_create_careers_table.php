<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('education_type_id')
                  ->constrained('education_types')
                  ->onDelete('cascade');
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->integer('total_credits')->default(0);
            $table->integer('theoretical_hours')->default(0);
            $table->integer('practical_hours')->default(0);
            $table->integer('duration_years')->default(0);
            $table->integer('duration_semesters')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['education_type_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('careers');
    }
};