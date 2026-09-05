<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateSubject extends Model
{
    protected $fillable = [
        'template_semester_id',
        'name',
        'code',
        'credits',
        'theoretical_hours',
        'practical_hours',
        'description',
        'order',
    ];

    protected $casts = [
        'credits' => 'integer',
        'theoretical_hours' => 'integer',
        'practical_hours' => 'integer',
        'order' => 'integer',
    ];

    // ✅ Relación con semestre
    public function templateSemester(): BelongsTo
    {
        return $this->belongsTo(TemplateSemester::class);
    }

    // ✅ Accessor para horas totales
    public function getTotalHoursAttribute()
    {
        return $this->theoretical_hours + $this->practical_hours;
    }
}