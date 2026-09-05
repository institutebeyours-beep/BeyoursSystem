<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeConfiguration extends Model
{
    use SoftDeletes;

    protected $table = 'grade_configurations';

    protected $fillable = [
        'course_id',
        'subject_id',
        'name',
        'is_active',
        'description',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ✅ Relación con curso
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // ✅ Relación con asignatura
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    // ✅ Relación con componentes
    public function components(): HasMany
    {
        return $this->hasMany(GradeComponent::class, 'grade_configuration_id');
    }

    // ✅ Relación con el usuario que creó
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ✅ Scope para activos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ✅ Calcular total de porcentaje
    public function getTotalPercentageAttribute()
    {
        return $this->components->sum('percentage');
    }

    // ✅ Verificar si está completo (100%)
    public function getIsCompleteAttribute()
    {
        return $this->components->sum('percentage') == 100;
    }
}