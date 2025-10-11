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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->text('description');
            $table->date('install_date')->nullable();
            $table->date('install_date_confirmed')->nullable();
            $table->decimal('price_to_customer', 10, 2)->nullable();
            $table->decimal('price_to_supplier', 10, 2)->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->integer('montage_time')->nullable();
            $table->boolean('is_requested')->default(false);
            $table->boolean('is_confirmed')->default(false);
            $table->boolean('is_delivered')->default(false);
            $table->boolean('is_finished')->default(false);
            $table->foreignId('user_id')->constrained();
            $table->foreignId('client_id')->constrained();
            $table->foreignId('place_id')->constrained();
            $table->foreignId('project_id')->nullable()->constrained();
            $table->foreignId('order_category_id')->nullable()->constrained();
            $table->timestamps();
            
            $table->index('number');
            $table->index('install_date');
            $table->index(['is_requested', 'is_confirmed', 'is_delivered', 'is_finished']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
