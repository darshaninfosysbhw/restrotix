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
        Schema::table('tenants', function (Blueprint $table) {
            // 1️⃣ Old column remove
            $table->dropColumn('subscription_plan');

            $table->renameColumn('trial_ends_at', 'subscription_ends_at');
            // 2️⃣ New plan_id column add
            $table->foreignId('plan_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();

            // 3️⃣ subscription_status add
            $table->enum('subscription_status', ['trial', 'active', 'expired', 'canceled'])
                ->default('trial')
                ->after('plan_id');

            // 5️⃣ ✅ NEW: Billing Cycle
            $table->enum('billing_cycle', ['monthly', 'yearly'])
                ->default('monthly')
                ->after('subscription_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {


            $table->dropForeign(['plan_id']);
            $table->dropColumn(['plan_id', 'subscription_status', 'billing_cycle']);
            $table->renameColumn('subscription_ends_at', 'trial_ends_at');

            // purana column wapas add
            $table->string('subscription_plan')->default('starter');
        });
    }
};
