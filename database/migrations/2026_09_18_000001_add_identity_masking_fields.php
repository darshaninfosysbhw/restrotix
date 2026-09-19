<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('allow_identity_masking')->default(false);
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('custom_identity_masking')->nullable()->default(null)->after('is_banned');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->enum('mask_scope', ['none', 'public_only', 'everywhere'])
                ->default('none')
                ->after('branch_name');
            $table->string('display_name', 150)->nullable()->after('mask_scope');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['mask_scope', 'display_name']);
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('custom_identity_masking');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('allow_identity_masking');
        });
    }
};
