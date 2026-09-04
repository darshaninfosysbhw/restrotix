<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Agar koi table abhi calling_waiter ya request_bill par atki hai toh pehle safe occupied status set karo
        DB::table('tables')
            ->whereIn('status', ['calling_waiter', 'request_bill'])
            ->update(['status' => 'occupied']);

        // 2. ENUM ko sanitize karo (calling_waiter & request_bill hatao)
        // Direct ALTER query standard MySQL ke liye best rehti hai bina doctrine dependency ke
        DB::statement("ALTER TABLE `tables` MODIFY COLUMN `status` ENUM('available', 'occupied', 'reserved', 'out_of_service') NOT NULL DEFAULT 'available'");

        // 3. New Boolean flags add karo
        Schema::table('tables', function (Blueprint $table) {
            $table->boolean('is_calling_waiter')->default(false)->after('status');
            $table->boolean('is_bill_requested')->default(false)->after('is_calling_waiter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback hone par flags drop karo
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn(['is_calling_waiter', 'is_bill_requested']);
        });

        // Wapas purana ENUM restore karo
        DB::statement("ALTER TABLE `tables` MODIFY COLUMN `status` ENUM('available', 'occupied', 'reserved', 'calling_waiter', 'request_bill', 'out_of_service') NOT NULL DEFAULT 'available'");
    }
};