<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_invoices', function (Blueprint $table) {
            $table->integer('item_count')->default(0)->after('invoice_number');
            $table->integer('total_qty')->default(0)->after('item_count');
            $table->decimal('subtotal_before_discount', 10, 2)->default(0.00)->after('total_qty');
            $table->decimal('item_discount_amount', 10, 2)->default(0.00)->after('subtotal_before_discount');
            $table->decimal('subtotal_after_item_discount', 10, 2)->default(0.00)->after('item_discount_amount');
            $table->decimal('overall_discount_percent', 5, 2)->default(0.00)->after('item_discount_amount');
            $table->decimal('overall_discount_amount', 10, 2)->default(0.00)->after('overall_discount_percent');
            $table->decimal('taxable_amount', 10, 2)->default(0.00)->after('overall_discount_amount');
            $table->string('tax_setting', 20)->default('exclusive')->after('overall_discount_amount');
            $table->decimal('tax_rate_snapshot', 5, 2)->default(0.00)->after('tax_rate');
            $table->string('payment_mode', 20)->default('paid')->after('status');
            $table->string('payment_method', 50)->nullable()->after('payment_mode');
            $table->decimal('tender_amount', 10, 2)->default(0.00)->after('payment_method');
            $table->decimal('change_amount', 10, 2)->default(0.00)->after('tender_amount');
            $table->decimal('paid_amount', 10, 2)->default(0.00)->after('change_amount');
            $table->decimal('due_amount', 10, 2)->default(0.00)->after('paid_amount');
            $table->string('customer_name_snapshot')->nullable()->after('due_amount');
            $table->string('table_number_snapshot', 50)->nullable()->after('customer_name_snapshot');
            $table->foreignId('cashier_user_id')->nullable()->after('table_number_snapshot')->constrained('users')->nullOnDelete();
            $table->text('notes_snapshot')->nullable()->after('cashier_user_id');

            $table->unique('order_id');
        });
    }

    public function down(): void
    {
        Schema::table('order_invoices', function (Blueprint $table) {
            $table->dropUnique(['order_id']);
            $table->dropConstrainedForeignId('cashier_user_id');
            $table->dropColumn([
                'item_count',
                'total_qty',
                'subtotal_before_discount',
                'item_discount_amount',
                'subtotal_after_item_discount',
                'overall_discount_percent',
                'overall_discount_amount',
                'taxable_amount',
                'tax_setting',
                'tax_rate_snapshot',
                'payment_mode',
                'payment_method',
                'tender_amount',
                'change_amount',
                'paid_amount',
                'due_amount',
                'customer_name_snapshot',
                'table_number_snapshot',
                'notes_snapshot',
            ]);
        });
    }
};
