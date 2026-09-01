<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // पुराने कॉलम्स को हटा रहे हैं क्योंकि अब डेटा 'plan_prices' में है
            $table->dropColumn(['monthly_price', 'yearly_price']);
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // रोलबैक के लिए वापस जोड़ना
            $table->decimal('monthly_price', 15, 2)->default(0.00);
            $table->decimal('yearly_price', 15, 2)->default(0.00);
        });
    }
};
