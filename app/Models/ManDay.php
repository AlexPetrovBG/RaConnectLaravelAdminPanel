<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManDay extends Model
{
    protected $fillable = [
        'date',
        'is_vacation',
        'is_medical',
        'price',
        'description',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'is_vacation' => 'boolean',
        'is_medical' => 'boolean',
        'price' => 'decimal:2',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function montages(): HasMany
    {
        return $this->hasMany(Montage::class);
    }
}
