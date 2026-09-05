<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('career_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('careers')
                  ->onDelete('set null');
            
            $table->index('career_id');
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['career_id']);
            $table->dropColumn('career_id');
        });
    }
};