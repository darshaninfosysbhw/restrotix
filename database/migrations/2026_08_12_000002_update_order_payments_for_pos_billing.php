<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_payments', function (Blueprint $table) {
            $table->string('payment_mode', 20)->default('paid')->after('invoice_id');
            $table->decimal('tender_amount', 10, 2)->default(0.00)->after('amount');
            $table->decimal('change_amount', 10, 2)->default(0.00)->after('tender_amount');
        });

        DB::statement(
            "ALTER TABLE order_payments MODIFY payment_method ENUM('fonepay_dynamic','static_qr','cash','card','nepal_pay','bank_transfer') NOT NULL"
        );
    }

    public function down(): void
    {
        DB::statement(
            "ALTER TABLE order_payments MODIFY payment_method ENUM('fonepay_dynamic','static_qr','cash','card') NOT NULL"
        );

        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropColumn(['payment_mode', 'tender_amount', 'change_amount']);
        });
    }
};
