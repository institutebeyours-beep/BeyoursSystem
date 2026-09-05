<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            
            // ✅ Información del backup
            $table->string('filename');
            $table->string('type')->default('full'); // full, database, files
            $table->string('size')->nullable(); // Tamaño formateado
            $table->bigInteger('size_bytes')->nullable(); // Tamaño en bytes
            
            // ✅ Relación con el usuario que lo creó
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            // ✅ Fechas
            $table->timestamp('created_at');
            
            // ✅ Para búsquedas rápidas
            $table->index(['type', 'created_at']);
            $table->index('filename');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};