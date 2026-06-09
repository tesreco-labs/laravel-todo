<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'title',
        'description',
        'is_completed',
        'status',
        'priority',
        'due_date',
        'completed_at',
        'user_id',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];
}
