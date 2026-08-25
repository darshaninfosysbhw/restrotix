<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->foreignKeyExists('order_items', 'order_items_invoice_id_foreign')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('invoice_id', 'order_items_invoice_id_foreign')
                ->references('id')
                ->on('order_invoices')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if ($this->foreignKeyExists('order_items', 'order_items_invoice_id_foreign')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropForeign('order_items_invoice_id_foreign');
            });
        }

        if ($this->indexExists('order_items', 'order_items_invoice_id_index')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropIndex('order_items_invoice_id_index');
            });
        }
    }

    private function foreignKeyExists(string $table, string $keyName): bool
    {
        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $keyName)
            ->exists();
    }

    private function indexExists(string $table, string $indexName): bool
    {
        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $indexName)
            ->exists();
    }
};
