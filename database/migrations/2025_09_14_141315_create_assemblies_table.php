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
        Schema::create('assemblies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->index();
            $table->foreignId('component_id')->index();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('restrict');
            $table->foreign('component_id')->references('id')->on('components')->onDelete('restrict');
            $table->uuid('company_guid')->nullable();
            $table->integer('cell_number')->nullable();
            $table->string('trolley', 128)->nullable()->index();
            $table->string('trolley_cell', 128)->nullable()->index();
            $table->binary('picture')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assemblies');
    }
};
