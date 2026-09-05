<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $table = 'backups';
    
    protected $fillable = [
        'filename',
        'type',
        'size',
        'size_bytes',
        'created_by',
        'created_at',
    ];

    public $timestamps = false;
}