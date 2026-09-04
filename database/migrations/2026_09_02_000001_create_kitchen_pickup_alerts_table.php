<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kitchen_pickup_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('table_id')->nullable()->constrained('tables')->nullOnDelete();
            $table->unsignedInteger('kot_number');
            $table->string('status', 20)->default('pending');
            $table->timestamp('ready_at');
            $table->timestamp('accepted_at')->nullable();
            $table->foreignId('accepted_by_waiter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['order_id', 'kot_number'], 'pickup_alert_order_kot_unique');
            $table->index(['tenant_id', 'branch_id', 'status'], 'pickup_alert_branch_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kitchen_pickup_alerts');
    }
};
