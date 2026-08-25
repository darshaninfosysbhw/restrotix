<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->foreignId('invoice_id')->constrained('order_invoices')->onDelete('cascade');
            $table->enum('payment_method', ['fonepay_dynamic', 'static_qr', 'cash', 'card']);
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->string('transaction_ref', 100)->nullable();
            $table->json('gateway_response')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->unsignedBigInteger('verified_by_user_id')->nullable();
            $table->timestamps();

            $table->index('transaction_ref');
            $table->index('status');
        });

        // Linking invoice_id back to order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('invoice_id')->references('id')->on('order_invoices')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
        });
        Schema::dropIfExists('order_payments');
    }
};
