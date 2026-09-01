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
        Schema::create('employee_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Specific Employee Info
            $table->string('employee_id')->unique()->nullable(); // e.g., RC-2026-001
            $table->string('designation')->nullable(); // Captain, Chef, etc.
            $table->string('pin_code', 6)->nullable(); // POS Login ke liye

            // Identity & Verification
            $table->string('id_type')->nullable(); // Aadhaar, PAN
            $table->string('id_number')->nullable();
            $table->string('id_proof_path')->nullable();

            // Personal & Medical
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();

            // Emergency
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_number')->nullable();

            // Employment & Salary
            $table->date('joining_date')->nullable();
            $table->date('exit_date')->nullable();
            $table->decimal('base_salary', 12, 2)->default(0.00);

            // Bank Details
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_details');
    }
};
