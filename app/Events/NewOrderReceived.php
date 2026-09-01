<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // 1. Ye interface zaroori hai
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderReceived implements ShouldBroadcast // 2. Yahan implements add kiya
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderData; // 3. Ye data admin ko dikhega

    public function __construct($orderData)
    {
        $this->orderData = $orderData;
    }

    public function broadcastOn(): array
    {
        $branchId = (int) ($this->orderData['branch_id'] ?? 0);

        if ($branchId <= 0) {
            return [];
        }

        return [
            new PrivateChannel('orders.branch.' . $branchId),
        ];
    }
}
