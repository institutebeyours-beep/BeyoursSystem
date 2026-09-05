// database/migrations/xxxx_xx_xx_create_attendance_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'justified'])->default('present');
            $table->text('observation')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->unique(['student_id', 'course_id', 'date']);
            $table->index(['course_id', 'date']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance');
    }
};