<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'address',
        'vat',
        'is_supplier',
        'is_client',
    ];

    protected $casts = [
        'is_supplier' => 'boolean',
        'is_client' => 'boolean',
    ];

    // Relationships
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
}
