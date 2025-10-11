<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderCategory extends Model
{
    protected $fillable = [
        'program_name',
        'humanlike_name',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    // Relationships
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
