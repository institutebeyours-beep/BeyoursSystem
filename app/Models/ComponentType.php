<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComponentType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'description',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function components(): HasMany
    {
        return $this->hasMany(GradeComponent::class, 'type_id');
    }

    // ✅ Scope para activos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ✅ Scope para ordenar
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    // ✅ Scope para default
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}