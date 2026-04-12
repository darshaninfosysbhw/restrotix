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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('table_id')->nullable()->constrained()->nullOnDelete();

            $table->string('order_number')->unique();
            $table->string('table_number')->nullable();

            // 🔥 ORDER TYPE (3 scenario)
            $table->enum('order_type', ['dine_in', 'takeaway', 'direct'])->default('dine_in');
            $table->string('source')->default('manual');

            // 🔥 BILLING BREAKDOWN
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);

            // 🔥 PAYMENT TRACK
            $table->decimal('paid_amount', 10, 2)->default(0);

            // 🔥 ORDER STATUS (HIGH LEVEL)
            $table->enum('status', [
                'running',
                'completed',
                'cancelled'
            ])->default('running');

            // 🔥 KITCHEN STATUS (CHEF FLOW)
            $table->enum('kitchen_status', [
                'pending',
                'confirmed',
                'preparing',
                'served'
            ])->default('pending');

            // 🔥 PAYMENT STATUS
            $table->enum('payment_status', [
                'pending',
                'partial',
                'paid'
            ])->default('pending');

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable() // Kyunki QR order me koi user nahi hota
                ->constrained('users') // Users table se link hai
                ->nullOnDelete();

            $table->timestamp('ordered_at')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
