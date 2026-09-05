<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'date',
        'status',
        'observation',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // 👨‍🎓 Estudiante
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // 📚 Curso
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // 👤 Creado por
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // 🔍 Scopes
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeJustified($query)
    {
        return $query->where('status', 'justified');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    // 📊 Estado como texto
    public function getStatusTextAttribute()
    {
        return [
            'present' => '✅ Presente',
            'absent' => '❌ Ausente',
            'justified' => '📝 Justificado',
        ][$this->status] ?? $this->status;
    }

    // 🎨 Color del estado
    public function getStatusColorAttribute()
    {
        return [
            'present' => 'green',
            'absent' => 'red',
            'justified' => 'yellow',
        ][$this->status] ?? 'gray';
    }
}