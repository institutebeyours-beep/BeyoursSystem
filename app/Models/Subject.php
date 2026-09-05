<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $table = 'subjects';

    protected $fillable = [
        'name',
        'code',
        'description',
        'credits',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credits' => 'integer',
    ];

    // ✅ Relación con cursos (muchos a muchos)
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_subject');
    }

    // ✅ Relación con configuraciones de calificaciones
    public function gradeConfigurations(): HasMany
    {
        return $this->hasMany(GradeConfiguration::class, 'subject_id');
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
}