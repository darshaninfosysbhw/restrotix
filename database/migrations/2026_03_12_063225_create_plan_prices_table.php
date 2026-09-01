<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');

            // मंथली और इयरली दोनों के लिए अलग कॉलम
            $table->decimal('monthly_price', 15, 2)->default(0.00);
            $table->decimal('yearly_price', 15, 2)->default(0.00);

            $table->timestamps();

            // एक प्लान की एक देश में एक ही प्राइस लिस्ट होनी चाहिए
            $table->unique(['plan_id', 'currency_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_prices');
    }
};
