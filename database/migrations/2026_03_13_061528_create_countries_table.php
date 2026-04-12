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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // India, Nepal
            $table->string('iso_code', 5)->unique(); // IN, NP
            $table->string('phone_code', 10)->nullable(); // +91, +977

            // आपकी पुरानी currencies टेबल से लिंक
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');

            $table->string('timezone')->default('UTC'); // Asia/Kolkata, Asia/Kathmandu
            $table->string('flag')->nullable(); // flag icon path or emoji
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
