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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // दुकान का नाम
            $table->string('category'); // जैसे: Vegetables, Dairy, Poultry
            $table->string('city');
            $table->decimal('commission_rate', 5, 2)->default(0.00); // आपका फायदा (SaaS Owner)
            $table->boolean('is_verified')->default(false); // सुपर एडमिन का ठप्पा
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
