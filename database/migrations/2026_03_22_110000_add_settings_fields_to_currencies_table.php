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
        Schema::table('currencies', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('symbol');
            $table->unsignedTinyInteger('decimal_places')->default(2)->after('exchange_rate');
            $table->enum('symbol_position', ['Prefix', 'Suffix'])->default('Prefix')->after('decimal_places');
            $table->boolean('is_default')->default(false)->after('symbol_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn(['country_id', 'decimal_places', 'symbol_position', 'is_default']);
        });
    }
};
