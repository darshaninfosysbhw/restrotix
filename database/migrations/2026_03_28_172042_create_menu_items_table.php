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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();

            // 🏢 SaaS & Logic
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade');

            // 🌳 Category Mapping
            $table->foreignId('category_id')->constrained('menu_categories')->onDelete('cascade');

            // 📝 Item Basic Info
            $table->string('name');
            $table->string('slug');
            $table->string('code')->nullable(); // SKU/Item Code
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            // 💰 Pricing & Tax
            $table->decimal('base_price', 10, 2)->default(0.00);
            $table->decimal('sale_price', 10, 2)->nullable(); // Discounted price if any
            $table->decimal('tax_percent', 5, 2)->default(0.00);

            // 🥗 Food Type
            $table->enum('type', ['veg', 'non-veg', 'egg', 'other'])->default('veg');

            // ⚙️ Status & Features
            $table->boolean('is_available')->default(true); // Stock check
            $table->boolean('is_active')->default(true);    // Global toggle
            $table->boolean('is_recommended')->default(false); // "Must Try" tag

            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Security: Duplicate code check per tenant
            $table->unique(['tenant_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
