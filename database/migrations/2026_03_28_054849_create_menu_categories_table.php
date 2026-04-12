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
        Schema::create('menu_categories', function (Blueprint $table) {
            $table->id();

            // 🏢 SaaS & Multi-branch Logic
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade'); // NULL = Global

            // 🌳 Hierarchy Logic
            $table->unsignedBigInteger('parent_id')->nullable();

            // 📝 Category Details
            $table->string('name');
            $table->string('slug'); // Removed unique() here, will handle uniqueness per tenant in logic
            $table->string('code')->nullable(); // The SKU Code (e.g., BEV-101)
            $table->string('image')->nullable();

            // ⚙️ Status & Sorting
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Relationships
            $table->foreign('parent_id')->references('id')->on('menu_categories')->onDelete('cascade');

            // 🔒 Security: Ek tenant ke andar duplicate code nahi hona chahiye
            $table->unique(['tenant_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_categories');
    }
};
