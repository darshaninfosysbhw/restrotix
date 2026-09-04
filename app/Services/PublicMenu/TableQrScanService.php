<?php

namespace App\Services\PublicMenu;

use App\Models\Table;
use App\Models\TableAccessSession;
use App\Models\TableQrScan;
use Illuminate\Http\Request;

class TableQrScanService
{
    public function recordScan(Table $table, Request $request, ?TableAccessSession $session = null): TableQrScan
    {
        $payload = [
            'tenant_id' => (int) $table->tenant_id,
            'branch_id' => (int) $table->branch_id,
            'table_id' => (int) $table->id,
            'table_access_session_id' => $session?->id,
            'qr_token' => (string) ($table->qr_token ?? ''),
            'session_token' => (string) ($session?->session_token ?? ''),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        if ($session?->id) {
            return TableQrScan::query()->firstOrCreate(
                ['table_access_session_id' => (int) $session->id],
                $payload
            );
        }

        return TableQrScan::query()->create($payload);
    }
}
