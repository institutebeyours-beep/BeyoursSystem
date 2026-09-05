<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateSemester extends Model
{
    protected $fillable = [
        'template_type_id',
        'semester_number',
        'total_hours',
        'total_credits',
        'description',
        'order',
    ];

    protected $casts = [
        'total_hours' => 'integer',
        'total_credits' => 'integer',
        'semester_number' => 'integer',
        'order' => 'integer',
    ];

    // ✅ Relación con plantilla
    public function templateType(): BelongsTo
    {
        return $this->belongsTo(TemplateType::class);
    }

    // ✅ Relación con asignaturas
    public function subjects(): HasMany
    {
        return $this->hasMany(TemplateSubject::class)->orderBy('order');
    }

    // ✅ Accessor para nombre formateado
    public function getFormattedNameAttribute()
    {
        return "{$this->semester_number}° Semestre";
    }
}