<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WaiterCalled implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $callData;

    public function __construct(array $callData)
    {
        $this->callData = $callData;
    }

    public function broadcastOn(): array
    {
        $branchId = (int) ($this->callData['branch_id'] ?? 0);
        if ($branchId <= 0) {
            return [];
        }

        return [
            new PrivateChannel('orders.branch.' . $branchId),
        ];
    }
}
