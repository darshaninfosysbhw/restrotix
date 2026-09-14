<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tables')
            ->select(['id', 'table_number'])
            ->whereNotNull('area_id')
            ->orderBy('id')
            ->chunkById(200, function ($tables): void {
                foreach ($tables as $table) {
                    $number = trim((string) $table->table_number);

                    // Handles legacy GF-01 after its area code was renamed to NPR.
                    if (preg_match('/^[A-Z0-9_]+-(\d+)$/i', $number, $matches) === 1) {
                        DB::table('tables')->where('id', $table->id)->update([
                            'table_number' => $matches[1],
                        ]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Historical prefixes cannot be reconstructed safely after an area rename.
    }
};
