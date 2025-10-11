<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'description',
        'deadline',
        'priority',
        'is_finished',
        'time_finished',
        'from_user_id',
        'to_user_id',
    ];

    protected $casts = [
        'deadline' => 'date',
        'is_finished' => 'boolean',
        'time_finished' => 'datetime',
    ];

    // Relationships
    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
