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
        Schema::create('table_service_requests', function (Blueprint $table) {
            $table->id();
           
            // Multi-tenant & Branch IDs
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('branch_id');
           
            // Table Reference
            $table->unsignedBigInteger('table_id');
           
            // Request Type: Call Waiter, Bill Request, aur Table Transfer
            $table->enum('type', ['call_waiter', 'bill_request', 'table_transfer'])->default('call_waiter');

            // Reason / Notes (e.g., 'VIP Table', 'Rush at Table 5', etc.)
            $table->string('notes')->nullable();
           
            // Initiator ya Handled By (Jis waiter ne initiate ya resolve kiya)
            $table->unsignedBigInteger('handled_by_waiter_id')->nullable();

            // Target Waiter (Kis waiter ko transfer bheja gaya hai; NULL = broadcast to all)
            $table->unsignedBigInteger('target_waiter_id')->nullable();
           
            // Request Status Lifecycle
            $table->enum('status', ['pending', 'accepted', 'completed', 'cancelled'])->default('pending');
           
            // Timestamps for Audit & Turnaround Time Tracking
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
           
            $table->timestamps();

            // Foreign Keys
            $table->foreign('table_id')
                  ->references('id')
                  ->on('tables')
                  ->onDelete('cascade');

            $table->foreign('handled_by_waiter_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->foreign('target_waiter_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            // Performance Indexes for Fast Realtime Floor Queries
            $table->index(['tenant_id', 'branch_id', 'status']);
            $table->index(['table_id', 'status']);
            $table->index(['handled_by_waiter_id', 'status']);
            $table->index(['target_waiter_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_service_requests');
    }
};