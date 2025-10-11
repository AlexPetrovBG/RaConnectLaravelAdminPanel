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
        Schema::create('montages', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('duration')->nullable();
            $table->boolean('confirmed')->default(false);
            $table->foreignId('user_id')->constrained();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('man_day_id')->nullable()->constrained();
            $table->timestamps();
            
            $table->index('date');
            $table->index('confirmed');
            $table->index(['user_id', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('montages');
    }
};
