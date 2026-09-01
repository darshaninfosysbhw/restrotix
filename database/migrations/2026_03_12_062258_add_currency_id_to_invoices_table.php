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
        Schema::table('invoices', function (Blueprint $table) {
            // 1. अगर पुरानी 'currency' स्ट्रिंग कॉलम है तो उसे हटा सकते हैं या रहने दें
            // 2. प्रोफेशनल currency_id कॉलम जोड़ना
            $table->foreignId('currency_id')
                ->nullable() // पुराने डेटा के लिए सुरक्षित रहेगा
                ->after('tenant_id')
                ->constrained('currencies')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });
    }
};
