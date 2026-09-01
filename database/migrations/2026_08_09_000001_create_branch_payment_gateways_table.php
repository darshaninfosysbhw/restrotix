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
        Schema::create('branch_payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_gateway_id')->constrained('payment_gateways')->cascadeOnDelete();
            $table->json('credentials')->nullable();
            $table->enum('mode', ['sandbox', 'live'])->default('sandbox');
            $table->enum('checkout_mode', ['dynamic_api', 'static_qr', 'disabled'])
                ->default('disabled');
            $table->boolean('is_active')->default(true);
            $table->string('static_qr_image')->nullable();
            $table->string('static_qr_label')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'branch_id', 'payment_gateway_id'], 'branch_payment_gateway_unique');
            $table->index(['tenant_id', 'branch_id', 'is_active'], 'branch_payment_gateway_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_payment_gateways');
    }
};
