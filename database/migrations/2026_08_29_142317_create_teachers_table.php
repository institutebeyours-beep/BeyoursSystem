<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('specialty')->nullable();
            $table->date('hire_date')->nullable();
            $table->text('bio')->nullable();
            $table->enum('status', ['active', 'inactive', 'on_leave'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['code', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('teachers');
    }
};