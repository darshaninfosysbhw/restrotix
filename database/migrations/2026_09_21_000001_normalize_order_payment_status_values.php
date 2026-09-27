<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_status', [
                'pending',
                'partial',
                'unpaid',
                'partially_paid',
                'paid',
            ])->default('unpaid')->change();
        });

        DB::table('orders')->where('payment_status', 'pending')->update(['payment_status' => 'unpaid']);
        DB::table('orders')->where('payment_status', 'partial')->update(['payment_status' => 'partially_paid']);

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_status', ['unpaid', 'partially_paid', 'paid'])
                ->default('unpaid')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_status', [
                'pending',
                'partial',
                'unpaid',
                'partially_paid',
                'paid',
            ])->default('pending')->change();
        });

        DB::table('orders')->where('payment_status', 'unpaid')->update(['payment_status' => 'pending']);
        DB::table('orders')->where('payment_status', 'partially_paid')->update(['payment_status' => 'partial']);

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'partial', 'paid'])
                ->default('pending')
                ->change();
        });
    }
};
