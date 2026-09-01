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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            $table->foreignId('menu_item_id')->nullable()->constrained()->nullOnDelete();

            $table->string('item_name');

            $table->decimal('price', 10, 2);

            $table->integer('quantity');

            $table->decimal('total', 10, 2);

            $table->text('notes')->nullable();


            // 🔥 CORE KITCHEN LOGIC START
            $table->enum('status', [
                'new',
                'preparing',
                'ready',
                'served',
                'rejected'
            ])->default('new');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('kitchen_type')->nullable();
            $table->integer('preparation_time')->nullable();
            $table->boolean('is_delayed')->default(false);
            $table->integer('priority')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
