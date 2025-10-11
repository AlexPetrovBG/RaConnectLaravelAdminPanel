<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentCategory extends Model
{
    protected $fillable = [
        'program_name',
        'humanlike_name',
        'type',
        'attributes',
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    // Relationships
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
