<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')
                ->default(0)
                ->after('slug')
                ->index();

            $table->string('plan_type', 30)
                ->default('standard')
                ->after('sort_order')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
            $table->dropIndex(['plan_type']);

            $table->dropColumn([
                'sort_order',
                'plan_type',
            ]);
        });
    }
};