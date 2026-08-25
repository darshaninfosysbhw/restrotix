<?php

use App\Models\TableAccessSession;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('table:sessions-expire', function () {
    $now = now();

    $expiredActive = TableAccessSession::query()
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
        ->where('status', 'grace')
        ->whereNotNull('grace_expires_at')
        ->where('grace_expires_at', '<=', $now)
        ->update([
            'status' => 'expired',
            'expires_at' => $now,
            'last_activity_at' => $now,
            'updated_at' => $now,
        ]);

    $this->info('Expired ' . ($expiredActive + $expiredGrace) . ' stale table access session(s).');
})->purpose('Expire stale table access sessions');

Schedule::command('table:sessions-expire')
    ->everyMinute()
    ->withoutOverlapping();
