<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'career_id',  // ✅ NUEVO: Relación con carrera
        'name',
        'code',
        'description',
        'course_type',
        'credits',
        'total_credits',
        'class_hours_per_week',
        'study_hours_per_week',
        'lab_hours_per_week',
        'total_hours_per_week',
        'total_weeks',
        'total_hours',
        'study_ratio',
        'lab_ratio',
        'duration',
        'schedule',
        'capacity',
        'status',
        'created_by',
    ];

    protected $casts = [
        'schedule' => 'array',
        'credits' => 'integer',
        'total_credits' => 'integer',
        'duration' => 'integer',
        'capacity' => 'integer',
        'class_hours_per_week' => 'decimal:2',
        'study_hours_per_week' => 'decimal:2',
        'lab_hours_per_week' => 'decimal:2',
        'total_hours_per_week' => 'decimal:2',
        'total_hours' => 'decimal:2',
        'study_ratio' => 'decimal:1',
        'lab_ratio' => 'decimal:1',
    ];

    // ========================================== //
    // RELACIONES
    // ========================================== //

    // ✅ Relación con carrera
    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    // 👤 Usuario que creó el curso
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ✅ Relación con inscripciones (estudiantes inscritos)
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    // ✅ Relación con estudiantes (a través de enrollments)
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'enrollments')
                    ->withPivot('enrollment_date', 'status', 'final_grade', 'notes')
                    ->withTimestamps();
    }

    // 📋 Asistencia
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    // ✅ Relación con asignaturas (a través de course_subject)
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'course_subject');
    }

    // ✅ Relación con configuraciones de calificaciones
    public function gradeConfigurations(): HasMany
    {
        return $this->hasMany(GradeConfiguration::class);
    }

    // ========================================== //
    // CRÉDITOS Y CÁLCULOS
    // ========================================== //

    // ✅ Calcular créditos usados
    public function getUsedCreditsAttribute()
    {
        return $this->subjects()->sum('credits') ?? 0;
    }

    // ✅ Calcular créditos disponibles
    public function getAvailableCreditsAttribute()
    {
        return max(0, $this->total_credits - $this->used_credits);
    }

    // ✅ Calcular porcentaje de progreso
    public function getCreditsProgressAttribute()
    {
        if ($this->total_credits == 0) return 0;
        return round(($this->used_credits / $this->total_credits) * 100, 2);
    }

    // ✅ Verificar si hay créditos disponibles
    public function hasAvailableCredits($credits = 0)
    {
        if ($this->total_credits == 0) return true;
        return $this->available_credits >= $credits;
    }

    // ========================================== //
    // SCOPES
    // ========================================== //

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
                     ->orWhere('code', 'LIKE', "%{$search}%");
    }

    public function scopeByCareer($query, $careerId)
    {
        return $query->where('career_id', $careerId);
    }

    // ========================================== //
    // ACCESSORS
    // ========================================== //

    // 📊 Obtener estudiantes activos
    public function getActiveStudentsCountAttribute()
    {
        return $this->students()->wherePivot('status', 'active')->count();
    }

    // 🎯 Obtener promedio de calificaciones
    public function getAverageGradeAttribute()
    {
        return $this->students()->avg('final_grade');
    }

    // ✅ Accessor para tipo de curso legible
    public function getCourseTypeLabelAttribute()
    {
        $types = [
            'theoretical' => '📖 Teórico',
            'theoretical_practical' => '📖🔬 Teórico-Práctico',
            'practical' => '🔬 Práctico (Laboratorio)',
            'specialized_lab' => '🧪 Laboratorio Especializado',
        ];
        return $types[$this->course_type] ?? $this->course_type;
    }
}