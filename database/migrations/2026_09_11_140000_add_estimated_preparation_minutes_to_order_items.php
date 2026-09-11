<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedSmallInteger('estimated_preparation_minutes')->nullable();
        });

        DB::table('order_items')->whereIn('status', ['new', 'pending', 'preparing'])
            ->orderBy('id')->chunkById(200, function ($items) {
                $times = DB::table('menu_items')->whereIn('id', $items->pluck('menu_item_id'))
                    ->pluck('preparation_time', 'id');
                foreach ($items as $item) {
                    DB::table('order_items')->where('id', $item->id)->update([
                        'estimated_preparation_minutes' => $times[$item->menu_item_id] ?? null,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('estimated_preparation_minutes');
        });
    }
};
