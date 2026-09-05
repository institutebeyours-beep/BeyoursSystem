<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role;

class ManualPdf extends Model
{
    protected $fillable = [
        'role_id',
        'file_name',
        'file_path',
        'file_size',
        'version',
        'is_active',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
        'uploaded_at' => 'datetime',
    ];

    // ✅ Relación con el rol (Spatie)
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // ✅ Relación con el usuario que subió el archivo
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ✅ Scope para activos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ✅ Accessor para URL completa
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    // ✅ Accessor para tamaño formateado
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    // ✅ Accessor para nombre del rol
    public function getRoleNameAttribute()
    {
        return $this->role?->name ?? 'Sin rol';
    }

    // ✅ Accessor para display name del rol
    public function getRoleDisplayNameAttribute()
    {
        $displayNames = [
            'super-admin' => '👑 Super Administrador',
            'admin' => '🔧 Administrador',
            'academico' => '📚 Académico',
            'docente' => '👨‍🏫 Docente',
            'estudiante' => '👨‍🎓 Estudiante',
        ];
        return $displayNames[$this->role?->name] ?? $this->role?->name ?? 'Sin rol';
    }
}