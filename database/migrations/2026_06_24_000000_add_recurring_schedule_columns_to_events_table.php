<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('events')) {
            return;
        }

        $addStartDate = !Schema::hasColumn('events', 'start_date');
        $addNextDate = !Schema::hasColumn('events', 'next_date');

        if ($addStartDate || $addNextDate) {
            Schema::table('events', function (Blueprint $table) use ($addStartDate, $addNextDate) {
                if ($addStartDate) {
                    $table->date('start_date')->nullable();
                }

                if ($addNextDate) {
                    $table->date('next_date')->nullable();
                }
            });
        }

        // Older installations stored start_date but not next_date. Reuse the
        // original start date so existing recurring events can run immediately.
        if (Schema::hasColumn('events', 'start_date')) {
            DB::table('events')
                ->whereNull('next_date')
                ->whereNotNull('start_date')
                ->update(['next_date' => DB::raw('start_date')]);
        }

        // Very old rows may not have had either date. A created-at fallback is
        // preferable to a null cursor, which previously caused fatal errors.
        DB::table('events')
            ->whereNull('start_date')
            ->update(['start_date' => DB::raw('DATE(created_at)')]);

        DB::table('events')
            ->whereNull('next_date')
            ->update(['next_date' => DB::raw('DATE(created_at)')]);
    }

    public function down(): void
    {
        // Deliberately retained: removing these columns would erase schedule
        // state from existing events during a rollback.
    }
};
