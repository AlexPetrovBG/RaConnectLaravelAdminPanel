<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = [
        'project_id',
        'component_id',
        'company_guid',
        'code',
        'code_no_color',
        'component_code',
        'category_designation',
        'consume_group_designation',
        'consume_group_priority',
        'cost_group_guid',
        'designation',
        'unit',
        'unit_weight',
        'length',
        'width',
        'height',
        'surface',
        'quantity',
        'angle1',
        'angle2',
        'bar_length',
        'position',
        'short_position',
        'is_extra',
    ];

    protected $casts = [
        'company_guid' => 'string',
        'cost_group_guid' => 'string',
        'consume_group_priority' => 'integer',
        'unit_weight' => 'decimal:4',
        'length' => 'decimal:4',
        'width' => 'decimal:4',
        'height' => 'decimal:4',
        'surface' => 'decimal:4',
        'quantity' => 'decimal:4',
        'angle1' => 'decimal:4',
        'angle2' => 'decimal:4',
        'bar_length' => 'decimal:4',
        'is_extra' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }
}
