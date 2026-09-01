<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('branch_id');
            $table->foreignId('table_id')->nullable()->constrained('tables')->nullOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('order_invoice_id')->nullable()->constrained('order_invoices')->nullOnDelete();
            $table->foreignId('branch_payment_gateway_id')->nullable()->constrained('branch_payment_gateways')->nullOnDelete();
            $table->string('gateway_slug', 50);
            $table->string('gateway_name')->nullable();
            $table->enum('checkout_mode', ['dynamic_api', 'static_qr', 'disabled']);
            $table->decimal('amount', 10, 2)->default(0);
            $table->char('currency_code', 10)->default('NPR');
            $table->string('provider_reference')->nullable();
            $table->string('payment_url')->nullable();
            $table->json('provider_request')->nullable();
            $table->json('provider_response')->nullable();
            $table->enum('status', ['pending', 'initiated', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('failure_reason')->nullable();
            $table->timestamp('initiated_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'branch_id', 'status'], 'payment_session_lookup');
            $table->index(['gateway_slug', 'provider_reference'], 'payment_session_provider_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_sessions');
    }
};
