<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('manual_pdfs', function (Blueprint $table) {
            $table->id();
            
            // ✅ Relación con la tabla roles de Spatie
            $table->foreignId('role_id')
                  ->constrained('roles')
                  ->onDelete('cascade')
                  ->unique(); // Un solo PDF por rol
            
            // ✅ Datos del archivo
            $table->string('file_name', 255);
            $table->string('file_path', 500);
            $table->integer('file_size')->default(0);
            $table->string('version', 20)->default('1.0');
            
            // ✅ Estado
            $table->boolean('is_active')->default(true);
            
            // ✅ Auditoría
            $table->foreignId('uploaded_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->timestamp('uploaded_at')->useCurrent();
            
            $table->timestamps();
            
            // ✅ Índices
            $table->index('role_id');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('manual_pdfs');
    }
};