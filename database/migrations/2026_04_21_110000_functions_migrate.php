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
        Schema::create('functions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            // $table->text('effect');
            $table->string('category');

            $table->foreign('category')
                ->references('category')
                ->on('categories')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('functions');
    }
};
