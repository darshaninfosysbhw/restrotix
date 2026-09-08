<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', fn (Blueprint $table) => $table->boolean('auto_accept_qr_orders')->default(false));
        Schema::create('qr_order_submissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_token')->unique();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('table_id')->constrained()->cascadeOnDelete();
            $table->foreignId('table_access_session_id')->constrained()->cascadeOnDelete();
            $table->string('request_key', 64)->nullable();
            $table->string('status', 20)->default('pending');
            $table->json('payload');
            $table->decimal('total', 12, 2);
            $table->unsignedInteger('quantity');
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('kot_number')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handled_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
            $table->unique(['table_access_session_id', 'request_key'], 'qr_submission_request_unique');
            $table->index(['tenant_id', 'branch_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_order_submissions');
        Schema::table('branches', fn (Blueprint $table) => $table->dropColumn('auto_accept_qr_orders'));
    }
};
