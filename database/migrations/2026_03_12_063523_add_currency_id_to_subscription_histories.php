<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_histories', function (Blueprint $table) {
            $table->foreignId('currency_id')
                ->nullable()
                ->after('amount') // amount के ठीक बाद ताकि पढ़ने में आसानी हो
                ->constrained('currencies')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_histories', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });
    }
};
