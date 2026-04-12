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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');

            $table->string('branch_name');
            $table->string('contact_number', 20);
            $table->string('branch_email')->nullable();
            $table->string('country_code', 3)->default('IN');
            $table->string('currency', 5)->default('INR');
            $table->string('timezone')->default('Asia/Kolkata');

            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->text('full_address')->nullable();

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->boolean('offline_billing_enabled')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'branch_name']);
            $table->index(['tenant_id', 'city', 'state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
