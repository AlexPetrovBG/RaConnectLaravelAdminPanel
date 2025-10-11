<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Montage extends Model
{
    protected $fillable = [
        'date',
        'duration',
        'confirmed',
        'user_id',
        'order_id',
        'man_day_id',
    ];

    protected $casts = [
        'date' => 'date',
        'confirmed' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function manDay(): BelongsTo
    {
        return $this->belongsTo(ManDay::class);
    }
}
