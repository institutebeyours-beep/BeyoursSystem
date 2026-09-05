<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeComponent extends Model
{
    protected $table = 'grade_components';

    protected $fillable = [
        'grade_configuration_id',
        'name',
        'type_id',
        'percentage',
        'max_grade',
        'description',
        'order',
        'is_required',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'max_grade' => 'decimal:2',
        'is_required' => 'boolean',
    ];

    // Relación con la configuración
    public function configuration(): BelongsTo
    {
        return $this->belongsTo(GradeConfiguration::class, 'grade_configuration_id');
    }

    // Relación con el tipo de componente
    public function type(): BelongsTo
    {
        return $this->belongsTo(ComponentType::class, 'type_id');
    }

    // Accessor para mantener compatibilidad con código existente
    public function getTypeSlugAttribute()
    {
        return $this->type ? $this->type->slug : 'other';
    }

    // Accessor para el nombre del tipo
    public function getTypeNameAttribute()
    {
        return $this->type ? $this->type->name : 'Otro';
    }

    // Accessor para el ícono del tipo
    public function getTypeIconAttribute()
    {
        return $this->type ? $this->type->icon : '📌';
    }

    // Accessor para el color del tipo
    public function getTypeColorAttribute()
    {
        return $this->type ? $this->type->color : 'gray';
    }
}