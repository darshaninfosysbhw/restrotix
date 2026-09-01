<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BillRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $requestData;

    public function __construct(array $requestData)
    {
        $this->requestData = $requestData;
    }

    public function broadcastOn(): array
    {
        $branchId = (int) ($this->requestData['branch_id'] ?? 0);
        if ($branchId <= 0) {
            return [];
        }

        return [
            new PrivateChannel('orders.branch.' . $branchId),
        ];
    }
}
