<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// fk van functions moet nog toegevoegd
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grid_cells', function (Blueprint $table) {
            $table->id();
            $table->integer('x_coordinate');
            $table->integer('y_coordinate');
            $table->boolean('is_available')->default(true);
            // $table->foreignIdFor(\App\Models\Function::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grid_cells');
    }
};
