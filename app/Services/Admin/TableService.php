<?php

namespace App\Services\Admin;

use App\Models\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TableService
{
    /**
     * Optimized Bulk Generation (High Performance)
     */
    public function generateBulkTables(array $data, int $tenantId)
    {
        $branchId = $data['branch_id'];
        $count    = $data['table_count'];
        $start    = $data['start_number'];
        $capacity = $data['capacity'] ?? 4;

        $tableNumbers = [];

        // Step 1: Generate all table numbers in memory
        for ($i = $start; $i < ($start + $count); $i++) {
            $tableNumbers[] = 'T-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        // Step 2: Fetch existing tables in ONE query (Fastest way)
        $existingTables = Table::where('tenant_id', $tenantId)
            ->where('branch_id', $branchId)
            ->whereIn('table_number', $tableNumbers)
            ->pluck('table_number')
            ->toArray();

        // Step 3: Filter and Prepare Data
        $newTables = [];
        foreach ($tableNumbers as $tableNumber) {
            if (!in_array($tableNumber, $existingTables)) {
                $newTables[] = [
                    'tenant_id'    => $tenantId,
                    'branch_id'    => $branchId,
                    'table_number' => $tableNumber,
                    'capacity'     => $capacity,
                    'status'       => 'available',
                    // Bulk insert model hooks bypass karta hai, isliye yahan manually token denge
                    'qr_token'     => 'RT-' . strtoupper(Str::random(6)) . '-' . rand(10, 99),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
        }

        // Step 4: Bulk insert with transaction for DB safety
        DB::beginTransaction();
        try {
            if (!empty($newTables)) {
                // Table::insert() use kar rahe hain bulk ke liye
                Table::insert($newTables);
            }

            DB::commit();
            return count($newTables);
        } catch (\Exception $e) {
            DB::rollBack();
            // Error log karna ya throw karna
            throw $e;
        }
    }

    /**
     * Update Single Table Details
     */
    public function updateTable(int $id, array $data, int $tenantId)
    {
        // 1. Check for Duplicate Table Number in the same branch
        $exists = Table::where('tenant_id', $tenantId)
            ->where('branch_id', $data['branch_id'])
            ->where('table_number', $data['table_number'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            throw new \Exception('Table number already exists in this branch!');
        }

        // 2. Update the Table
        $table = Table::where('tenant_id', $tenantId)->findOrFail($id);

        return $table->update([
            'branch_id'    => $data['branch_id'],
            'table_number' => $data['table_number'],
            'capacity'     => $data['capacity'],
            'status'       => $data['status'],
        ]);
    }
}
