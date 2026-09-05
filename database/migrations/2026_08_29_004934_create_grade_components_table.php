<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grade_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_configuration_id')
                  ->constrained('grade_configurations')
                  ->onDelete('cascade');
            $table->string('name');
            
            // ✅ CAMBIO: type_id en lugar de type
            $table->foreignId('type_id')
                  ->constrained('component_types')
                  ->onDelete('restrict');
            
            $table->integer('order')->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->decimal('max_grade', 8, 2)->default(100);
            $table->boolean('is_required')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index('grade_configuration_id');
            $table->index('type_id');
            
            // ✅ CAMBIO: Índice único sin type
            $table->unique(['grade_configuration_id', 'order'], 'unique_component_order');
        });
    }

    public function down()
    {
        Schema::dropIfExists('grade_components');
    }
};