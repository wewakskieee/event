<?php
// database/migrations/2024_01_08_000000_create_logs_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs_transaction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->string('action'); // created, status_changed
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_transaction');
    }
};
