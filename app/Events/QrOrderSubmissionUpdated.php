<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class QrOrderSubmissionUpdated implements ShouldBroadcastNow
{
    public function __construct(public array $submissionData) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('qr-approvals.branch.'.(int) $this->submissionData['branch_id'])];
    }
}
