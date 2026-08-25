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
        Schema::table('order_items', function (Blueprint $table) {
            $table->timestamp('rejected_at')->nullable()->after('served_at');
            $table->unsignedBigInteger('invoice_id')->nullable()->after('order_id');
            $table->decimal('applied_discount', 10, 2)->default(0.00)->after('price');
        });
        DB::statement("UPDATE order_items SET rejected_at = updated_at WHERE status = 'rejected' AND rejected_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['rejected_at', 'invoice_id', 'applied_discount']);
        });
    }
};
