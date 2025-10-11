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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->date('deadline')->nullable();
            $table->string('priority')->default('medium');
            $table->boolean('is_finished')->default(false);
            $table->timestamp('time_finished')->nullable();
            $table->foreignId('from_user_id')->constrained('users');
            $table->foreignId('to_user_id')->constrained('users');
            $table->timestamps();
            
            $table->index('deadline');
            $table->index('priority');
            $table->index('is_finished');
            $table->index(['from_user_id', 'to_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
