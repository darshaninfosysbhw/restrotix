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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Stripe, Khalti, Razorpay
            $table->string('slug')->unique(); // e.g., stripe, khalti

            // Sabhi API keys aur Secrets yahan JSON format mein rahenge
            $table->json('credentials')->nullable();

            // Kaun si currencies support karta hai (e.g., ["USD", "NPR"])
            $table->json('supported_currencies')->nullable();

            $table->string('mode')->default('sandbox'); // sandbox or live
            $table->boolean('is_active')->default(false);
            $table->string('logo')->nullable(); // Gateway icon path
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
