<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pieces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->index();
            $table->foreignId('component_id')->nullable()->index();
            $table->foreignId('assembly_id')->nullable()->index();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('restrict');
            $table->foreign('component_id')->references('id')->on('components')->onDelete('restrict');
            $table->foreign('assembly_id')->references('id')->on('assemblies')->onDelete('restrict');
            $table->uuid('company_guid')->nullable();
            $table->string('barcode', 128)->nullable()->index();
            $table->string('piece_code', 128)->nullable();
            $table->string('component_number', 128)->nullable()->index();
            $table->string('component_code', 128)->nullable()->index();
            $table->string('component_description', 256)->nullable();
            $table->string('project_number', 128)->nullable();
            $table->string('project_description', 256)->nullable();
            $table->string('project_code_parent', 128)->nullable();
            $table->string('project_phase', 128)->nullable();
            $table->string('profile_type', 50)->nullable();
            $table->string('profile_type_ra', 50)->nullable();
            $table->string('profile_code', 128)->nullable()->index();
            $table->string('profile_code_with_color', 256)->nullable();
            $table->string('profile_name', 256)->nullable();
            $table->string('profile_color', 128)->nullable();
            $table->integer('profile_width')->nullable();
            $table->integer('profile_height')->nullable();
            $table->integer('assembly_width')->nullable();
            $table->integer('assembly_height')->nullable();
            $table->integer('angle_left')->nullable();
            $table->integer('angle_right')->nullable();
            $table->integer('inner_length')->nullable();
            $table->integer('outer_length')->nullable();
            $table->integer('other_length')->nullable();
            $table->integer('bar_length_int')->nullable();
            $table->integer('bar_rest')->nullable();
            $table->string('bar_id', 128)->nullable();
            $table->integer('welding_tolerance')->nullable();
            $table->integer('bar_cutting_tolerance')->nullable();
            $table->string('cutting_pattern', 128)->nullable();
            $table->string('orientation', 50)->nullable();
            $table->string('material_type', 50)->nullable();
            $table->string('gasket', 128)->nullable();
            $table->string('reinforcement_code', 128)->nullable();
            $table->integer('reinforcement_length')->nullable();
            $table->string('segment_order', 50)->nullable();
            $table->string('trolley', 50)->nullable();
            $table->string('trolley_size', 50)->nullable();
            $table->string('trolley_cell', 128)->nullable();
            $table->string('parent_assembly_trolley_cell', 128)->nullable();
            $table->string('glazing_bead_trolley_cell', 128)->nullable();
            $table->string('cell', 50)->nullable();
            $table->string('client', 128)->nullable();
            $table->string('dealer', 128)->nullable();
            $table->string('water_handle', 128)->nullable();
            $table->string('info2', 256)->nullable();
            $table->string('info3', 256)->nullable();
            $table->text('hardware_info')->nullable();
            $table->text('glass_info')->nullable();
            $table->text('operations')->nullable();
            $table->binary('picture')->nullable();
            $table->timestamps();
            
            // Composite index
            $table->index(['project_id', 'component_id', 'assembly_id']);
        });
        
        // Add expression index for LOWER(barcode)
        DB::statement('CREATE INDEX pieces_barcode_lower_idx ON pieces (LOWER(barcode)) WHERE barcode IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop expression index first
        DB::statement('DROP INDEX IF EXISTS pieces_barcode_lower_idx');
        Schema::dropIfExists('pieces');
    }
};
