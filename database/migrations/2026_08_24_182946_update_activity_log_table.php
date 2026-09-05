<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('activity_log', function (Blueprint $table) {
            // ✅ Agregar columnas faltantes para versión 5.x
            if (!Schema::hasColumn('activity_log', 'attribute_changes')) {
                $table->json('attribute_changes')->nullable()->after('properties');
            }
            if (!Schema::hasColumn('activity_log', 'event')) {
                $table->string('event')->nullable()->after('attribute_changes');
            }
        });
    }

    public function down()
    {
        Schema::table('activity_log', function (Blueprint $table) {
            if (Schema::hasColumn('activity_log', 'attribute_changes')) {
                $table->dropColumn('attribute_changes');
            }
            if (Schema::hasColumn('activity_log', 'event')) {
                $table->dropColumn('event');
            }
        });
    }
};