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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('company_name'); // जैसे: "Haldiram's Group"
            $table->string('owner_name');
            $table->string('logo')->nullable();
            $table->string('subscription_plan')->default('starter'); // starter, growth, enterprise
            $table->date('trial_ends_at')->nullable();
            $table->boolean('is_banned')->default(false); // सुपर एडमिन यहाँ से बंद कर सकता है
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
