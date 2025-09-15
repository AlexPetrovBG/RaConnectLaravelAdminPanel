<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->index();
            $table->foreignId('component_id')->index();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('restrict');
            $table->foreign('component_id')->references('id')->on('components')->onDelete('restrict');
            $table->uuid('company_guid')->nullable();
            $table->string('code', 128)->nullable()->index();
            $table->string('code_no_color', 128)->nullable();
            $table->string('component_code', 128)->nullable();
            $table->string('category_designation', 128)->nullable();
            $table->string('consume_group_designation', 128)->nullable();
            $table->integer('consume_group_priority')->nullable();
            $table->uuid('cost_group_guid')->nullable();
            $table->string('designation', 256)->nullable();
            $table->string('unit', 50)->nullable();
            $table->decimal('unit_weight', 18, 4)->nullable();
            $table->decimal('length', 18, 4)->nullable();
            $table->decimal('width', 18, 4)->nullable();
            $table->decimal('height', 18, 4)->nullable();
            $table->decimal('surface', 18, 4)->nullable();
            $table->decimal('quantity', 18, 4)->nullable();
            $table->decimal('angle1', 18, 4)->nullable();
            $table->decimal('angle2', 18, 4)->nullable();
            $table->decimal('bar_length', 18, 4)->nullable();
            $table->string('position', 128)->nullable();
            $table->string('short_position', 128)->nullable();
            $table->boolean('is_extra')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
