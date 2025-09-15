<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'company_guid',
        'company_name',
        'checksum',
        'in_production',
        'name',
        'description',
    ];

    protected $casts = [
        'company_guid' => 'string',
        'in_production' => 'boolean',
    ];

    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
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
