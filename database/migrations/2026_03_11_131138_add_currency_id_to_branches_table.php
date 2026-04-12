<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // 1. पुराना स्ट्रिंग वाला कॉलम सीधा उड़ा दो
            if (Schema::hasColumn('branches', 'currency')) {
                $table->dropColumn('currency');
            }

            // 2. नया प्रोफेशनल ID वाला कॉलम जोड़ो
            $table->foreignId('currency_id')
                ->nullable()
                ->after('country_code')
                ->constrained('currencies')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
            $table->string('currency')->nullable()->after('country_code');
        });
    }
};
