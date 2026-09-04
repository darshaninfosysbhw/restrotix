<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('table_id')->constrained('tables')->cascadeOnDelete();
            $table->string('table_number', 50)->index();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('held_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('payload_json');
            $table->timestamp('held_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['tenant_id', 'table_id'], 'billing_drafts_tenant_table_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_drafts');
    }
};
