<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('orders.branch.{branchId}', function ($user, $branchId) {
    $userBranchId = (int) ($user->branch_id ?? 0);
    $requestedBranchId = (int) $branchId;

    if ($requestedBranchId <= 0 || !$user->tenant_id) {
        return false;
    }

    $belongsToTenant = \App\Models\Branch::withoutGlobalScope('country_filter')
        ->where('tenant_id', $user->tenant_id)->whereKey($requestedBranchId)->exists();
    if (!$belongsToTenant) {
        return false;
    }

    if (in_array((string) ($user->role ?? ''), ['superadmin', 'admin'], true)) {
        return true;
    }

    return $userBranchId === $requestedBranchId;
});
