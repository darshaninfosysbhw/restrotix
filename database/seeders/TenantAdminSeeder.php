<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Service;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;

class TenantAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. एक डमी Tenant (Restaurant/Company) बनाओ
        $tenant = Tenant::create([
            'company_name' => 'Grand Spice Restaurant',
            'owner_name' => 'John Doe',
            'subscription_plan' => 'premium',
            'is_banned' => false,
        ]);

        // 2. एक Branch बनाओ (क्योंकि Admin किसी न किसी ब्रांच का होगा)
        $branch = Branch::create([
            'tenant_id' => $tenant->id,
            'branch_name' => 'Main Center Branch',
            'location' => 'Mumbai, India',
            'contact_number' => '9876543210',
        ]);

        // 3. मास्टर Services बनाओ (Add-ons)
        $billingService = Service::create([
            'name' => 'Billing System',
            'slug' => 'billing',
            'price' => 1000
        ]);

        $inventoryService = Service::create([
            'name' => 'Inventory Management',
            'slug' => 'inventory',
            'price' => 1500
        ]);

        // 4. इस Tenant को ये Services असाइन करो (Pivot Table में)
        $tenant->services()->attach([
            $billingService->id => ['status' => 'active', 'expires_at' => now()->addYear()],
            $inventoryService->id => ['status' => 'active', 'expires_at' => now()->addYear()],
        ]);

        // 5. अब एक Admin User बनाओ जो इस Tenant को मैनेज करेगा
        User::create([
            'name' => 'Grand Admin',
            'email' => 'admin@resto.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'tenant_id' => $tenant->id,
            'branch_id' => $branch->id,
        ]);

        $this->command->info('Tenant, Services and Admin User created successfully!');
    }
}
