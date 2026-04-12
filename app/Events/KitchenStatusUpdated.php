<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KitchenStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $kitchenData;

    public function __construct(array $kitchenData)
    {
        $this->kitchenData = $kitchenData;
    }

    public function broadcastOn(): array
    {
        $branchId = (int) ($this->kitchenData['branch_id'] ?? 0);
        if ($branchId <= 0) {
            return [];
        }

        return [
            new PrivateChannel('orders.branch.' . $branchId),
        ];
    }
}

