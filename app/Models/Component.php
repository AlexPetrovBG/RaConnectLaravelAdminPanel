<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Component extends Model
{
    protected $fillable = [
        'project_id',
        'code',
        'company_guid',
        'designation',
        'picture',
        'quantity',
    ];

    protected $casts = [
        'company_guid' => 'string',
        'picture' => 'binary',
        'quantity' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assemblies(): HasMany
    {
        return $this->hasMany(Assembly::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function pieces(): HasMany
    {
        return $this->hasMany(Piece::class);
    }
}
