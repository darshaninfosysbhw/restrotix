<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class StaffUserSeeder extends Seeder
{
    public function run(): void
    {
        // मान लेते हैं कि तुम्हारे Admin का Tenant ID 1 है।
        // अगर कुछ और है तो यहाँ बदल लेना।
        $tenantId = 1;
        $branchId = 1;
        $staff = [
            [
                'name' => 'Suresh Sales',
                'email' => 'sales@test.com',
                'role' => 'sales_manager',
            ],
            [
                'name' => 'Ankit Accounts',
                'email' => 'accounts@test.com',
                'role' => 'account_manager',
            ],
            [
                'name' => 'Vikram Inventory',
                'email' => 'purchase@test.com', // जैसा web.php में है
                'role' => 'purchase_manager',
            ],
            [
                'name' => 'Rahul Chef',
                'email' => 'chef@test.com',
                'role' => 'chef',
            ],
            [
                'name' => 'Raya Manager',
                'email' => 'manager@test.com',
                'role' => 'manager',
            ],
            [
                'name' => 'Raya Auditor',
                'email' => 'auditor@test.com',
                'role' => 'auditor',
            ],
            [
                'name' => 'Raya Store Keeper',
                'email' => 'store_keeper@test.com',
                'role' => 'store_keeper',
            ],
            [
                'name' => 'Raya Waiter',
                'email' => 'waiter@test.com',
                'role' => 'waiter',
            ],
        ];

        foreach ($staff as $user) {
            User::updateOrCreate(
                ['email' => $user['email']], // अगर पहले से है तो अपडेट करेगा
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password123'),
                    'role' => $user['role'],
                    'tenant_id' => $tenantId,
                    'branch_id' => $branchId,
                ]
            );
        }
    }
}
