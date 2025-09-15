<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assembly extends Model
{
    protected $fillable = [
        'project_id',
        'component_id',
        'company_guid',
        'cell_number',
        'trolley',
        'trolley_cell',
        'picture',
    ];

    protected $casts = [
        'company_guid' => 'string',
        'cell_number' => 'integer',
        'picture' => 'binary',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    public function pieces(): HasMany
    {
        return $this->hasMany(Piece::class);
    }
}
