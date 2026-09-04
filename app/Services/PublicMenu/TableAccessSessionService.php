<?php

namespace App\Services\PublicMenu;

use App\Models\Table;
use App\Models\TableAccessSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TableAccessSessionService
{
    private const DEFAULT_SESSION_MINUTES = 30;
    private const TABLE_GRACE_MINUTES = 2;

    public function bootstrapFromScan(Table $table, Request $request): TableAccessSession
    {
        return DB::transaction(function () use ($table, $request) {
            $this->expireStaleSessionsForTable($table);

            $existingSession = $this->findReusableSession($table);

            if ($existingSession) {
                $existingSession->forceFill($this->refreshPayload($table, $request, $existingSession));
                $existingSession->save();

                return $existingSession->refresh();
            }

            $graceSession = $this->findGraceSession($table);

            if ($graceSession) {
                return $graceSession->refresh();
            }

            return TableAccessSession::query()->create($this->createPayload($table, $request));
        });
    }

    public function findReusableSession(Table $table): ?TableAccessSession
    {
        return TableAccessSession::query()
            ->where('table_id', (int) $table->id)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest('id')
            ->first();
    }

    public function findGraceSession(Table $table): ?TableAccessSession
    {
        return TableAccessSession::query()
            ->where('table_id', (int) $table->id)
            ->where('status', 'grace')
            ->where(function ($query) {
                $query->whereNull('grace_expires_at')
                    ->orWhere('grace_expires_at', '>', now());
            })
            ->latest('id')
            ->first();
    }

    public function findValidSessionForTable(Table $table, string $sessionToken): ?TableAccessSession
    {
        $sessionToken = trim($sessionToken);

        if ($sessionToken === '') {
            return null;
        }

        $this->expireStaleSessionsForTable($table);

        return TableAccessSession::query()
            ->where('table_id', (int) $table->id)
            ->where('session_token', $sessionToken)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest('id')
            ->first();
    }

    public function getLatestSessionForTable(Table $table): ?TableAccessSession
    {
        return TableAccessSession::query()
            ->where('table_id', (int) $table->id)
            ->latest('id')
            ->first();
    }

    public function touchSession(TableAccessSession $session, Request $request): TableAccessSession
    {
        $lat = $request->filled('client_latitude') ? (float) $request->input('client_latitude') : null;
        $lng = $request->filled('client_longitude') ? (float) $request->input('client_longitude') : null;

        $session->forceFill([
            'status' => 'active',
            'last_activity_at' => now(),
            'expires_at' => now()->addMinutes(self::DEFAULT_SESSION_MINUTES),
            'client_latitude' => $lat ?? $session->client_latitude,
            'client_longitude' => $lng ?? $session->client_longitude,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ])->save();

        return $session->refresh();
    }

    public function releaseTable(Table $table, int $graceMinutes = self::TABLE_GRACE_MINUTES): int
    {
        $graceUntil = now()->addMinutes(max($graceMinutes, 1));

        return TableAccessSession::query()
            ->where('table_id', (int) $table->id)
            ->where('status', 'active')
            ->update([
                'status' => 'grace',
                'grace_expires_at' => $graceUntil,
                'expires_at' => $graceUntil,
                'last_activity_at' => now(),
                'updated_at' => now(),
            ]);
    }

    public function expireStaleSessionsForTable(Table $table): int
    {
        $now = now();

        $expiredActive = TableAccessSession::query()
            ->where('table_id', (int) $table->id)
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->update([
                'status' => 'expired',
                'grace_expires_at' => null,
                'last_activity_at' => $now,
                'updated_at' => $now,
            ]);

        $expiredGrace = TableAccessSession::query()
            ->where('table_id', (int) $table->id)
            ->where('status', 'grace')
            ->whereNotNull('grace_expires_at')
            ->where('grace_expires_at', '<=', $now)
            ->update([
                'status' => 'expired',
                'expires_at' => $now,
                'last_activity_at' => $now,
                'updated_at' => $now,
            ]);

        return $expiredActive + $expiredGrace;
    }

    private function createPayload(Table $table, Request $request): array
    {
        return array_merge($this->basePayload($table, $request), [
            'session_token' => $this->generateSessionToken(),
            'started_at' => now(),
        ]);
    }

    private function refreshPayload(Table $table, Request $request, ?TableAccessSession $existingSession = null): array
    {
        return array_merge($this->basePayload($table, $request), [
            'status' => 'active',
            'last_activity_at' => now(),
            'expires_at' => now()->addMinutes(self::DEFAULT_SESSION_MINUTES),
            'client_latitude' => $request->filled('client_latitude')
                ? (float) $request->input('client_latitude')
                : $existingSession?->client_latitude,
            'client_longitude' => $request->filled('client_longitude')
                ? (float) $request->input('client_longitude')
                : $existingSession?->client_longitude,
        ]);
    }

    private function basePayload(Table $table, Request $request): array
    {
        $lat = $request->filled('client_latitude') ? (float) $request->input('client_latitude') : null;
        $lng = $request->filled('client_longitude') ? (float) $request->input('client_longitude') : null;

        return [
            'tenant_id' => (int) $table->tenant_id,
            'branch_id' => (int) $table->branch_id,
            'table_id' => (int) $table->id,
            'status' => 'active',
            'last_activity_at' => now(),
            'expires_at' => now()->addMinutes(self::DEFAULT_SESSION_MINUTES),
            'grace_expires_at' => null,
            'client_latitude' => $lat,
            'client_longitude' => $lng,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];
    }

    private function generateSessionToken(): string
    {
        return 'TAS-' . (string) Str::ulid();
    }
}
