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
        Schema::table('branches', function (Blueprint $table) {
            $table->string('tax_setting')->default('exclusive')->after('offline_billing_enabled');
            $table->decimal('tax_rate', 5, 2)->default(5.00)->after('tax_setting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('tax_setting');
            $table->dropColumn('tax_rate');
        });
    }
};
