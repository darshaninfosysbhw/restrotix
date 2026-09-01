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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Indian Rupee, Nepalese Rupee
            $table->string('code', 3)->unique(); // INR, NPR
            $table->string('symbol', 10); // ₹, Rs.
            $table->decimal('exchange_rate', 15, 6)->default(1.00); // Base Currency के मुकाबले
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
