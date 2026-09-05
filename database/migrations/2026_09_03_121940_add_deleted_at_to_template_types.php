<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('template_types', function (Blueprint $table) {
            if (!Schema::hasColumn('template_types', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down()
    {
        Schema::table('template_types', function (Blueprint $table) {
            if (Schema::hasColumn('template_types', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};