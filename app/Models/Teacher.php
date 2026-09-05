<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Teacher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'code',
        'specialty',
        'hire_date',
        'bio',
        'status',
        'created_by',
    ];

    protected $casts = [
        'hire_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'teacher_course')
                    ->withPivot('year', 'semester', 'status')
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