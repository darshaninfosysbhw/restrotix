<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {

            if (!Schema::hasColumn('branches', 'is_vat_registered')) {
                $table->boolean('is_vat_registered')
                      ->default(false)
                      ->after('offline_billing_enabled');
            }
        
            if (!Schema::hasColumn('branches', 'pan_vat_number')) {
                $table->string('pan_vat_number', 50)
                      ->nullable()
                      ->after('tax_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'is_vat_registered',
                'pan_vat_number'
            ]);
        });
    }
};
