<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'theoretical_hours')) {
                $table->integer('theoretical_hours')->default(0)->after('credits');
            }
            if (!Schema::hasColumn('subjects', 'practical_hours')) {
                $table->integer('practical_hours')->default(0)->after('theoretical_hours');
            }
        });
    }

    public function down()
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'theoretical_hours')) {
                $table->dropColumn('theoretical_hours');
            }
            if (Schema::hasColumn('subjects', 'practical_hours')) {
                $table->dropColumn('practical_hours');
            }
        });
    }
};