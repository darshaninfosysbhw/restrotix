<?php

namespace Tests\Unit;

use App\Models\OrderItem;
use App\Services\OrderPreparationTimerService;
use Carbon\Carbon;
use Tests\TestCase;

class OrderPreparationTimerTest extends TestCase
{
    private function item(string $status, ?int $minutes, ?string $started = null): OrderItem
    {
        return new OrderItem(['status' => $status, 'estimated_preparation_minutes' => $minutes, 'started_at' => $started, 'quantity' => 4]);
    }

    public function test_waiting_uses_maximum_not_sum_or_quantity_and_does_not_start(): void
    {
        $timer = (new OrderPreparationTimerService)->build(collect([
            $this->item('new', 20), $this->item('new', 5), $this->item('rejected', 60),
        ]));
        $this->assertSame('waiting', $timer['state']);
        $this->assertSame(20, $timer['estimated_minutes']);
        $this->assertNull($timer['ready_at']);
    }

    public function test_staggered_starts_use_latest_deadline_and_refresh_does_not_reset_it(): void
    {
        $items = collect([
            $this->item('preparing', 20, '2026-09-11 12:00:00'),
            $this->item('preparing', 10, '2026-09-11 12:15:00'),
            $this->item('ready', 60, '2026-09-11 12:00:00'),
            $this->item('new', 30),
        ]);
        $service = new OrderPreparationTimerService;
        $timer = $service->build($items);
        $this->assertSame('preparing', $timer['state']);
        $this->assertSame(Carbon::parse('2026-09-11 12:25:00')->toIso8601String(), $timer['ready_at']);
        $this->assertSame($timer['ready_at'], $service->build($items)['ready_at']);
        $this->assertTrue($timer['has_waiting_items']);
        $this->assertSame(30, $timer['waiting_minutes']);
    }

    public function test_missing_time_does_not_invent_a_countdown(): void
    {
        $timer = (new OrderPreparationTimerService)->build(collect([$this->item('preparing', null, '2026-09-11 12:00:00')]));
        $this->assertSame('preparing', $timer['state']);
        $this->assertTrue($timer['has_unknown_time']);
        $this->assertNull($timer['ready_at']);
    }

    public function test_expired_estimate_keeps_preparing_until_kitchen_marks_ready(): void
    {
        $service = new OrderPreparationTimerService;
        $item = $this->item('preparing', 1, '2020-01-01 12:00:00');
        $this->assertSame('preparing', $service->build(collect([$item]))['state']);
        $item->status = 'ready';
        $timer = $service->build(collect([$item, $this->item('rejected', 30)]));
        $this->assertSame('ready', $timer['state']);
        $this->assertNull($timer['ready_at']);
        $this->assertSame('unavailable', $service->build(collect([$this->item('rejected', 30)]))['state']);
    }
}
