<?php
// database/migrations/2024_01_02_000000_create_events_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('image')->nullable();
            $table->string('location');
            $table->decimal('price', 10, 2);
            $table->integer('quota');
            $table->integer('quota_remaining');
            $table->dateTime('event_date');
            $table->enum('status', ['draft', 'published', 'ended'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('event_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
