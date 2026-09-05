<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->integer('credits')->default(0);
            $table->integer('duration')->nullable(); // horas
            $table->json('schedule')->nullable(); // {days: ["Lunes", "Miércoles"], time: "10:00-12:00"}
            $table->integer('capacity')->default(20);
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes(); // Eliminación lógica
            $table->timestamps();
            
            $table->index(['code', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('courses');
    }
};