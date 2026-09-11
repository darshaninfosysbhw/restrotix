<?php

namespace App\Services;

use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class OrderPreparationTimerService
{
    public function build(Collection $items): array
    {
        $now = now();

        // 1. Rejected ya Cancelled items filter out karo
        $active = $items->reject(fn (OrderItem $item) => in_array($item->status, ['rejected', 'cancelled'], true));
        
        // 2. Jo ban chuke hain (ready/served) unhe nikalo
        $unfinished = $active->reject(fn (OrderItem $item) => in_array($item->status, ['ready', 'served'], true));
        
        // 3. Waiting vs Preparing items
        $waiting = $unfinished->filter(fn (OrderItem $item) => $item->status !== 'preparing' || ! $item->started_at);
        $preparing = $unfinished->filter(fn (OrderItem $item) => $item->status === 'preparing' && $item->started_at);

        // 4. Sabhi preparing items ka target ready time nikalo
        $deadlines = $preparing->filter(fn (OrderItem $item) => (int) $item->estimated_preparation_minutes > 0)
            ->map(function (OrderItem $item) {
                $startedAt = $item->started_at instanceof Carbon ? $item->started_at : Carbon::parse($item->started_at);
                return $startedAt->copy()->addMinutes((int) $item->estimated_preparation_minutes);
            });

        // Sabse aakhiri ready hone wala time (Max Deadline)
        /** @var Carbon|null $latestDeadline */
        $latestDeadline = $deadlines->sortDesc()->first();

        // Agar koi naya waiting item hai jiska time purane deadline se bhi aage ja sakta hai
        if ($waiting->isNotEmpty()) {
            $maxWaitingMinutes = (int) $waiting->max('estimated_preparation_minutes');
            if ($maxWaitingMinutes > 0) {
                $waitingTarget = $now->copy()->addMinutes($maxWaitingMinutes);
                if (! $latestDeadline || $waitingTarget->greaterThan($latestDeadline)) {
                    $latestDeadline = $waitingTarget;
                }
            }
        }

        // Remaining time calculate karo (seconds me)
        $remainingSeconds = 0;
        $remainingMinutes = 0;

        if ($latestDeadline && $latestDeadline->isFuture()) {
            $remainingSeconds = $now->diffInSeconds($latestDeadline);
            $remainingMinutes = (int) ceil($remainingSeconds / 60);
        }

        return [
            'state' => $active->isEmpty() 
                ? 'unavailable' 
                : ($unfinished->isEmpty() ? 'ready' : ($preparing->isEmpty() ? 'waiting' : 'preparing')),
            
            // Yahan static initial prep time ki jagah actual bacha hua dynamic time bhejo
            'estimated_minutes' => $remainingMinutes > 0 ? $remainingMinutes : (int) $unfinished->max('estimated_preparation_minutes'),
            'remaining_seconds' => $remainingSeconds,
            'waiting_minutes' => (int) $waiting->max('estimated_preparation_minutes'),
            'has_waiting_items' => $waiting->isNotEmpty(),
            'has_unknown_time' => $unfinished->contains(fn (OrderItem $item) => ! $item->estimated_preparation_minutes),
            'ready_at' => $latestDeadline?->toIso8601String(),
            'server_now' => $now->toIso8601String(),
        ];
    }
}
