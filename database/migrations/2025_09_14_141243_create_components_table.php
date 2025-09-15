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
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->index();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('restrict');
            $table->string('code', 128)->nullable()->index();
            $table->uuid('company_guid')->nullable();
            $table->string('designation', 128)->nullable();
            $table->binary('picture')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};
