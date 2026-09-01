<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('tables')) {
            return;
        }

        DB::statement("ALTER TABLE `tables` MODIFY `status` ENUM('available', 'occupied', 'reserved', 'calling_waiter', 'request_bill', 'out_of_service') NOT NULL DEFAULT 'available'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('tables')) {
            return;
        }

        DB::statement("ALTER TABLE `tables` MODIFY `status` ENUM('available', 'occupied', 'reserved', 'calling_waiter', 'out_of_service') NOT NULL DEFAULT 'available'");
    }
};
