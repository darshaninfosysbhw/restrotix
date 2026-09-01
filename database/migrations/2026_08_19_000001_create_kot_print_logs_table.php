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
        Schema::create('kot_print_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('table_id')->constrained('tables')->cascadeOnDelete();
            $table->string('table_number', 50);
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('kot_number');
            $table->foreignId('printed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('printed_by_name')->nullable();
            $table->string('print_source', 32)->default('drawer');
            $table->timestamps();

            $table->index(
                ['tenant_id', 'branch_id', 'table_number', 'kot_number'],
                'kot_print_logs_lookup_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kot_print_logs');
    }
};
