<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_invoices', function (Blueprint $table) {

            // Remove global unique constraint
            $table->dropUnique('order_invoices_invoice_number_unique');

            // Invoice number unique only inside same tenant + same branch
            $table->unique(
                ['tenant_id', 'branch_id', 'invoice_number'],
                'order_invoices_tenant_branch_invoice_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('order_invoices', function (Blueprint $table) {

            $table->dropUnique(
                'order_invoices_tenant_branch_invoice_unique'
            );
        });
    }
};