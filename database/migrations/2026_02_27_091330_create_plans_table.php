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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Single Outlet, Multi-Branch
            $table->string('slug')->unique(); // single-outlet, multi-branch

            $table->decimal('monthly_price', 10, 2)->default(0);
            $table->decimal('yearly_price', 10, 2)->default(0);

            $table->integer('max_branches')->default(1);
            // plan ke hisaab se branch limit
            $table->json('features')->nullable();
            // Marketing के लिए फीचर्स की लिस्ट

            $table->integer('trial_days')->default(0);
            // फ्री ट्रायल के दिन

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
