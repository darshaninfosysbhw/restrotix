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
        Schema::table('tenants', function (Blueprint $table) {
            // country_id को plan_id के बाद जोड़ रहे हैं ताकि टेबल का स्ट्रक्चर साफ़ रहे
            $table->foreignId('country_id')
                ->after('plan_id')
                ->nullable()
                ->constrained('countries')
                ->onDelete('set null');

            // अगर आप चाहते हैं कि currency_id हटा दिया जाए तो यहाँ drop कर सकते हैं,
            // लेकिन अभी सुरक्षित रहने के लिए इसे रहने देते हैं।
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
        });
    }
};
