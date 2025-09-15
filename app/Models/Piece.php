<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Piece extends Model
{
    protected $fillable = [
        'project_id',
        'component_id',
        'assembly_id',
        'company_guid',
        'barcode',
        'piece_code',
        'component_number',
        'component_code',
        'component_description',
        'project_number',
        'project_description',
        'project_code_parent',
        'project_phase',
        'profile_type',
        'profile_type_ra',
        'profile_code',
        'profile_code_with_color',
        'profile_name',
        'profile_color',
        'profile_width',
        'profile_height',
        'assembly_width',
        'assembly_height',
        'angle_left',
        'angle_right',
        'inner_length',
        'outer_length',
        'other_length',
        'bar_length_int',
        'bar_rest',
        'bar_id',
        'welding_tolerance',
        'bar_cutting_tolerance',
        'cutting_pattern',
        'orientation',
        'material_type',
        'gasket',
        'reinforcement_code',
        'reinforcement_length',
        'segment_order',
        'trolley',
        'trolley_size',
        'trolley_cell',
        'parent_assembly_trolley_cell',
        'glazing_bead_trolley_cell',
        'cell',
        'client',
        'dealer',
        'water_handle',
        'info2',
        'info3',
        'hardware_info',
        'glass_info',
        'operations',
        'picture',
    ];

    protected $casts = [
        'company_guid' => 'string',
        'profile_width' => 'integer',
        'profile_height' => 'integer',
        'assembly_width' => 'integer',
        'assembly_height' => 'integer',
        'angle_left' => 'integer',
        'angle_right' => 'integer',
        'inner_length' => 'integer',
        'outer_length' => 'integer',
        'other_length' => 'integer',
        'bar_length_int' => 'integer',
        'bar_rest' => 'integer',
        'welding_tolerance' => 'integer',
        'bar_cutting_tolerance' => 'integer',
        'reinforcement_length' => 'integer',
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

    public function assembly(): BelongsTo
    {
        return $this->belongsTo(Assembly::class);
    }
}
