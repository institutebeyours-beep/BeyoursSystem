<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'student_code',  // ✅ Cambiado de 'code' a 'student_code'
        'enrollment_date',
        'phone',
        'address',
        'birth_date',
        'guardian_name',
        'guardian_phone',
        'status',
        'created_by',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'birth_date' => 'date',
    ];

    // ========================================== //
    // RELACIONES
    // ========================================== //

    // 👤 Usuario relacionado
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // 👤 Usuario que lo creó
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ✅ Relación con carreras (a través de student_careers)
    public function careers(): BelongsToMany
    {
        return $this->belongsToMany(Career::class, 'student_careers')
                    ->withPivot('enrollment_date', 'status', 'notes')
                    ->withTimestamps();
    }

    // ✅ Relación con cursos (a través de enrollments)
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
                    ->withPivot('enrollment_date', 'status', 'final_grade', 'notes')
                    ->withTimestamps();
    }

    // 📊 Calificaciones
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    // 📋 Asistencia
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    // ========================================== //
    // SCOPES
    // ========================================== //

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('student_code', 'LIKE', "%{$search}%")
                     ->orWhereHas('user', function ($q) use ($search) {
                         $q->where('name', 'LIKE', "%{$search}%")
                           ->orWhere('email', 'LIKE', "%{$search}%");
                     });
    }

    public function scopeByCareer($query, $careerId)
    {
        return $query->whereHas('careers', function ($q) use ($careerId) {
            $q->where('career_id', $careerId);
        });
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->whereHas('courses', function ($q) use ($courseId) {
            $q->where('course_id', $courseId);
        });
    }

    // ========================================== //
    // ACCESSORS
    // ========================================== //

    // ✅ Método para obtener nombre completo
    public function getFullNameAttribute()
    {
        return $this->user?->name ?? 'Sin nombre';
    }

    // ✅ Método para obtener email
    public function getEmailAttribute()
    {
        return $this->user?->email ?? 'Sin email';
    }

    // ✅ Obtener carreras activas
    public function getActiveCareersAttribute()
    {
        return $this->careers()->wherePivot('status', 'active')->get();
    }

    // ✅ Obtener cursos activos
    public function getActiveCoursesAttribute()
    {
        return $this->courses()->wherePivot('status', 'active')->get();
    }

    // ✅ Obtener porcentaje de asistencia
    public function getAttendancePercentageAttribute()
    {
        $total = $this->attendances()->count();
        if ($total === 0) return 0;
        
        $present = $this->attendances()->where('status', 'present')->count();
        return round(($present / $total) * 100, 2);
    }

    // ✅ Obtener promedio general
    public function getAverageGradeAttribute()
    {
        return $this->grades()->avg('grade') ?? 0;
    }
}