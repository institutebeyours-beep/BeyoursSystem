<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCareer extends Model
{
    protected $table = 'student_careers';

    protected $fillable = [
        'student_id',
        'career_id',
        'enrollment_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
    ];

    // ✅ Relación con estudiante
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // ✅ Relación con carrera
    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
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
}