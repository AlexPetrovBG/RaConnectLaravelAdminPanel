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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->date('date');
            $table->boolean('is_paid')->default(false);
            $table->string('type');
            $table->string('contragent_type');
            $table->unsignedBigInteger('contragent_id');
            $table->foreignId('order_id')->nullable()->constrained();
            $table->foreignId('document_category_id')->nullable()->constrained();
            $table->timestamps();
            
            $table->index('file_name');
            $table->index('date');
            $table->index(['contragent_type', 'contragent_id']);
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
