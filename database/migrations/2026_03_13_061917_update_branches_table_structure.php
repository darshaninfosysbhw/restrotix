<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // 1. पहले पुराना country_code कॉलम हटाते हैं
            $table->dropColumn('country_code');

            // 2. अब नया country_id कॉलम जोड़ते हैं
            $table->foreignId('country_id')
                ->after('branch_email')
                ->nullable() // शुरुआत में nullable ताकि माइग्रेशन न रुके
                ->constrained('countries')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');

            // वापस पुराना कॉलम लाना हो तो (Rollback के लिए)
            $table->string('country_code', 10)->nullable()->after('branch_email');
        });
    }
};
