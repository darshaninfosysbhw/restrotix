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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique(); // e.g., INV-2026-0001
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // pending, paid, failed, refunded
            $table->string('payment_method')->nullable(); // Khalti, Esewa, Bank Transfer
            $table->string('transaction_id')->nullable()->unique(); // Gateway Transaction ID
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
