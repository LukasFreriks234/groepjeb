<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_grid', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grid_cell_id')->constrained('grid_cells')->cascadeOnDelete();
            $table->string('item_type');
            $table->foreignId('function_id')->nullable()->constrained('functions')->nullOnDelete();
            $table->foreignId('event_id')->nullable()->constrained('events')->cascadeOnDelete();
            $table->foreignId('recurring_id')->nullable()->constrained('recurrings')->nullOnDelete();
            $table->dateTime('occurs_at')->nullable();
            $table->unsignedInteger('route_order')->nullable();
            $table->timestamps();

            $table->index(['grid_cell_id', 'item_type']);
            $table->index(['event_id', 'occurs_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_grid');
    }
};