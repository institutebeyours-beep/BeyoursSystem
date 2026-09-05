<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'education_type_id',
        'is_default',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ✅ Relación con tipo de enseñanza
    public function educationType(): BelongsTo
    {
        return $this->belongsTo(EducationType::class);
    }

    // ✅ Relación con semestres
    public function semesters(): HasMany
    {
        return $this->hasMany(TemplateSemester::class)->orderBy('order');
    }

    // ✅ Relación con usuario creador
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ✅ Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // ✅ Accessor para total de créditos
    public function getTotalCreditsAttribute()
    {
        return $this->semesters->sum('total_credits');
    }

    // ✅ Accessor para total de horas
    public function getTotalHoursAttribute()
    {
        return $this->semesters->sum('total_hours');
    }

    // ✅ Accessor para total de semestres
    public function getTotalSemestersAttribute()
    {
        return $this->semesters->count();
    }
}