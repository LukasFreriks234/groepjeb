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
        Schema::create('effects', function (Blueprint $table) {
            $table->foreignId('id')
                ->primary()
                ->constrained('functions')
                ->cascadeOnDelete();
            $table->integer("Veiligheid");
            $table->integer("Recreatie");
            $table->integer("Milieukwaliteit");
            $table->integer("Voorzieningen");
            $table->integer("Mobiliteit");
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('effects');
    }
};
