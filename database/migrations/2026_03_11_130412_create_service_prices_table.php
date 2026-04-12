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
        Schema::create('service_prices', function (Blueprint $table) {
            $table->id();

            // Service के साथ जोड़ो
            $table->foreignId('service_id')
                ->constrained('services')
                ->onDelete('cascade');

            // Currency के साथ जोड़ो
            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->onDelete('cascade');

            // उस खास करेंसी के लिए कीमत
            $table->decimal('price', 15, 2)->default(0.00);

            $table->timestamps();

            // PM Tip: एक सर्विस की एक करेंसी में एक ही कीमत होनी चाहिए,
            // इसलिए हम इन दोनों का 'Unique Constraint' बना रहे हैं।
            $table->unique(['service_id', 'currency_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_prices');
    }
};
