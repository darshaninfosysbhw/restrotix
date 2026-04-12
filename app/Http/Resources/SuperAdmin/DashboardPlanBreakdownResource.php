<?php

namespace App\Http\Resources\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardPlanBreakdownResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $totalTenants = (int) ($request->attributes->get('dashboard_total_tenants') ?? 0);
        $count = (int) ($this->total ?? 0);
        $percent = $totalTenants > 0 ? round(($count / $totalTenants) * 100, 1) : 0;

        return [
            'plan' => ucfirst($this->subscription_plan ?? 'starter'),
            'total' => $count,
            'percent' => $percent,
        ];
    }
}
