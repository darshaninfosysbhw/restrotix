<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tables')
            ->select(['id', 'area_id', 'table_number'])
            ->orderBy('id')
            ->chunkById(200, function ($tables): void {
                $areaCodes = DB::table('areas')
                    ->whereIn('id', $tables->pluck('area_id')->filter()->unique())
                    ->pluck('code', 'id');

                foreach ($tables as $table) {
                    $number = trim((string) $table->table_number);
                    $prefix = $table->area_id
                        ? strtoupper(trim((string) ($areaCodes[$table->area_id] ?? '')))
                        : 'T';

                    if ($prefix !== '' && str_starts_with(strtoupper($number), $prefix . '-')) {
                        DB::table('tables')->where('id', $table->id)->update([
                            'table_number' => substr($number, strlen($prefix) + 1),
                        ]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Prefixes are presentation data and may have changed since this migration.
        // Re-applying them on rollback would corrupt the permanent local number.
    }
};
