<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('functions', function (Blueprint $table) {

            $table->unsignedBigInteger(
                'related_function_id'
            )->nullable();

            $table->integer(
                'relationship_safety'
            )->default(0);

            $table->integer(
                'relationship_recreation'
            )->default(0);

            $table->integer(
                'relationship_environmental'
            )->default(0);

            $table->integer(
                'relationship_services'
            )->default(0);

            $table->integer(
                'relationship_mobility'
            )->default(0);

        });
    }

    public function down(): void
    {
        Schema::table('functions', function (Blueprint $table) {

            $table->dropColumn([

                'related_function_id',

                'relationship_safety',

                'relationship_recreation',

                'relationship_environmental',

                'relationship_services',

                'relationship_mobility',
            ]);

        });
    }
};
