<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'partial',
        'grade',
        'grade_final',
        'observations',
        'created_by',
    ];

    protected $casts = [
        'grade' => 'decimal:2',
        'grade_final' => 'decimal:2',
        'partial' => 'integer',
    ];

    // 👨‍🎓 Estudiante
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // 📚 Curso
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // 👤 Creado por
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // 🔍 Scopes
    public function scopeApproved($query)
    {
        return $query->where('grade_final', '>=', 60);
    }

    public function scopeFailed($query)
    {
        return $query->where('grade_final', '<', 60)
                     ->whereNotNull('grade_final');
    }

    // 📊 Estado de aprobación
    public function getIsApprovedAttribute()
    {
        if ($this->grade_final === null) return null;
        return $this->grade_final >= 60;
    }

    // 🎯 Estado como texto
    public function getStatusTextAttribute()
    {
        if ($this->grade_final === null) return 'Pendiente';
        return $this->grade_final >= 60 ? 'Aprobado' : 'Reprobado';
    }

    // 🎨 Color del estado
    public function getStatusColorAttribute()
    {
        if ($this->grade_final === null) return 'yellow';
        return $this->grade_final >= 60 ? 'green' : 'red';
    }
}