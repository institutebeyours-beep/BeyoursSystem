<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'enrollment_date',
        'status',
        'final_grade',
        'notes',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'final_grade' => 'decimal:2',
    ];

    // ✅ Relación con estudiante
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // ✅ Relación con curso
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // ✅ Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }
}