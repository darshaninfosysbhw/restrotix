<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('orders.branch.{branchId}', function ($user, $branchId) {
    $userBranchId = (int) ($user->branch_id ?? 0);
    $requestedBranchId = (int) $branchId;

    if ($requestedBranchId <= 0) {
        return false;
    }

    if (in_array((string) ($user->role ?? ''), ['superadmin', 'admin'], true) && $userBranchId === 0) {
        return true;
    }

    return $userBranchId === $requestedBranchId;
});
