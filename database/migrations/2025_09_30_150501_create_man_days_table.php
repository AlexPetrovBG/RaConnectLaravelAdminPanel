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
        Schema::create('man_days', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->boolean('is_vacation')->default(false);
            $table->boolean('is_medical')->default(false);
            $table->decimal('price', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
            
            $table->index('date');
            $table->index(['is_vacation', 'is_medical']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('man_days');
    }
};
