<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('effects', function (Blueprint $table) {
            if (Schema::hasColumn('effects', 'sensitive_proximity_penalty')) {
                $table->dropColumn('sensitive_proximity_penalty');
            }
        });
    }

    public function down(): void
    {
        Schema::table('effects', function (Blueprint $table) {
            $table->integer('sensitive_proximity_penalty')->default(0)->after('Mobility');
        });
    }
};