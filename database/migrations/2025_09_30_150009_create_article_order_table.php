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
        Schema::create('article_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained();
            $table->foreignId('order_id')->constrained();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('is_requested')->default(false);
            $table->boolean('is_confirmed')->default(false);
            $table->boolean('is_delivered')->default(false);
            $table->timestamps();
            
            $table->unique(['article_id', 'order_id']);
            $table->index(['is_requested', 'is_confirmed', 'is_delivered']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_order');
    }
};
