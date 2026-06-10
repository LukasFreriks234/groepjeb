<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'group_relationship_effects',
            function (Blueprint $table) {

                $table->integer('safety')->default(0);
                $table->integer('recreation')->default(0);
                $table->integer('environmental_quality')->default(0);
                $table->integer('services')->default(0);
                $table->integer('mobility')->default(0);

            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'group_relationship_effects',
            function (Blueprint $table) {

                $table->dropColumn([
                    'safety',
                    'recreation',
                    'environmental_quality',
                    'services',
                    'mobility',
                ]);

            }
        );
    }
};