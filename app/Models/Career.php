<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Career extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'education_type_id',
        'name',
        'code',
        'description',
        'total_credits',
        'theoretical_hours',
        'practical_hours',
        'duration_years',
        'duration_semesters',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_credits' => 'integer',
        'theoretical_hours' => 'integer',
        'practical_hours' => 'integer',
        'duration_years' => 'integer',
        'duration_semesters' => 'integer',
    ];

    // ✅ Relación con tipo de enseñanza
    public function educationType(): BelongsTo
    {
        return $this->belongsTo(EducationType::class);
    }

    // ✅ Relación con cursos
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    // ✅ Relación con estudiantes (a través de student_careers)
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_careers')
                    ->withPivot('enrollment_date', 'status', 'notes')
                    ->withTimestamps();
    }

    // ✅ Relación con asignaturas (a través de cursos)
    public function subjects(): BelongsToMany
    {
        return $this->hasManyThrough(
            Subject::class,
            Course::class,
            'career_id', // Foreign key on courses table
            'id', // Foreign key on subjects table
            'id', // Local key on careers table
            'id' // Local key on courses table
        );
    }

    // ✅ Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
                     ->orWhere('code', 'LIKE', "%{$search}%");
    }

    // ✅ Accessor para créditos usados
    public function getUsedCreditsAttribute()
    {
        return $this->courses->sum(function ($course) {
            return $course->subjects->sum('credits');
        });
    }

    // ✅ Accessor para créditos disponibles
    public function getAvailableCreditsAttribute()
    {
        return $this->total_credits - $this->used_credits;
    }

    // ✅ Accessor para progreso
    public function getCreditsProgressAttribute()
    {
        if ($this->total_credits == 0) return 0;
        return round(($this->used_credits / $this->total_credits) * 100, 2);
    }

    // ✅ Accessor para estudiantes activos
    public function getActiveStudentsCountAttribute()
    {
        return $this->students()->wherePivot('status', 'active')->count();
    }
}