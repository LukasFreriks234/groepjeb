<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('related_group_id')->constrained('groups')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['group_id', 'related_group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_relationships');
    }
};