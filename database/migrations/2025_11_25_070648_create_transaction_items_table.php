<?php
// database/migrations/2024_01_05_000000_create_transaction_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
            
            $table->index('transaction_id');
            $table->index('event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
