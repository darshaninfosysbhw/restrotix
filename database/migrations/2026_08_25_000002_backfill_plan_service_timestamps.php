<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('plan_service')
            ->whereNull('created_at')
            ->orWhereNull('updated_at')
            ->update([
                'created_at' => $now,
                'updated_at' => $now,
            ]);
    }

    public function down(): void
    {
        DB::table('plan_service')
            ->update([
                'created_at' => null,
                'updated_at' => null,
            ]);
    }
};
