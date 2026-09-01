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
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('kot_number')->default(1)->after('order_id');
            $table->foreignId('menu_item_variant_id')->nullable()->after('menu_item_id')->constrained('menu_item_variants')->nullOnDelete();
            $table->string('variant_name')->nullable()->after('item_name'); // e.g., 'Half Plate'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['menu_item_variant_id']);
            $table->dropColumn(['kot_number', 'menu_item_variant_id', 'variant_name']);
        });
    }
};
