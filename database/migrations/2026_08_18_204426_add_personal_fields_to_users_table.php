<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Datos personales
            $table->string('lastname')->nullable()->after('name');
            $table->string('second_lastname')->nullable()->after('lastname');
            $table->date('birth_date')->nullable()->after('second_lastname');
            $table->text('address')->nullable()->after('birth_date');
            
            // Datos de contacto
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('cellphone', 20)->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'lastname',
                'second_lastname',
                'birth_date',
                'address',
                'phone',
                'cellphone'
            ]);
        });
    }
};