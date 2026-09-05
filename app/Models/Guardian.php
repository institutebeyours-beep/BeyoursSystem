<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Guardian extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'relationship',
        'phone',
        'emergency_phone',
        'address',
        'status',
        'created_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'guardian_student')
                    ->withPivot('relationship', 'is_primary', 'status')
                    ->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFullNameAttribute()
    {
        return $this->user?->name ?? 'Sin nombre';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}