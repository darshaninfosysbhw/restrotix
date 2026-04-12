<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\Service;
use App\Models\Currency;
use App\Models\PlanPrice;
use App\Models\ServicePrice;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Currencies Get Karo (INR and NPR)
        $inr = Currency::where('code', 'INR')->first();
        $npr = Currency::where('code', 'NPR')->first();

        if (!$inr || !$npr) {
            $this->command->error('Bhai, pahle CurrencySeeder run karo! INR/NPR nahi mile.');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Create Services & Service Prices
        |--------------------------------------------------------------------------
        */
        $servicesData = [
            ['name' => 'Inventory Management', 'slug' => 'inventory', 'desc' => 'Stock, purchase and supplier operations.'],
            ['name' => 'Menu Management', 'slug' => 'menu-management', 'desc' => 'Menu items, variants and pricing control.'],
            ['name' => 'Reporting', 'slug' => 'reporting', 'desc' => 'Sales and business intelligence reports.'],
            ['name' => 'Priority Support', 'slug' => 'priority-support', 'desc' => 'Fastest email/phone support.'],
            ['name' => 'Advanced Analytics', 'slug' => 'advanced-analytics', 'desc' => 'Deep insights and forecasting.'],
        ];

        $services = [];
        foreach ($servicesData as $s) {
            $service = Service::updateOrCreate(
                ['slug' => $s['slug']],
                ['name' => $s['name'], 'description' => $s['desc']]
            );

            // Add Prices for Service (Example: Sab free rakha hai as per your old seeder)
            ServicePrice::updateOrCreate(['service_id' => $service->id, 'currency_id' => $inr->id], ['price' => 0]);
            ServicePrice::updateOrCreate(['service_id' => $service->id, 'currency_id' => $npr->id], ['price' => 0]);

            $services[$s['slug']] = $service;
        }

        /*
        |--------------------------------------------------------------------------
        | Plans Setup (Logic: Create Plan -> Link Services -> Set Multi-Country Prices)
        |--------------------------------------------------------------------------
        */

        // --- 1. Single Outlet ---
        $plan1 = Plan::updateOrCreate(
            ['slug' => 'single-outlet'],
            [
                'name' => 'Single Outlet',
                'max_branches' => 1,
                'trial_days' => 14,
                'features' => ['1 Branch Only', 'Menu Management', 'Basic Reporting', 'Email Support'],
                'is_active' => true,
            ]
        );
        $plan1->services()->sync([$services['menu-management']->id, $services['reporting']->id]);

        // Plan 1 Prices (INR vs NPR)
        PlanPrice::updateOrCreate(['plan_id' => $plan1->id, 'currency_id' => $inr->id], ['monthly_price' => 79.00, 'yearly_price' => 790.00]);
        PlanPrice::updateOrCreate(['plan_id' => $plan1->id, 'currency_id' => $npr->id], ['monthly_price' => 125.00, 'yearly_price' => 1250.00]);

        // --- 2. Multi-Branch ---
        $plan2 = Plan::updateOrCreate(
            ['slug' => 'multi-branch'],
            [
                'name' => 'Multi-Branch',
                'max_branches' => 5,
                'trial_days' => 30,
                'features' => ['Up to 5 Branches', 'Inventory Management', 'Menu Management', 'Advanced Reporting'],
                'is_active' => true,
            ]
        );
        $plan2->services()->sync([$services['inventory']->id, $services['menu-management']->id, $services['reporting']->id]);

        // Plan 2 Prices
        PlanPrice::updateOrCreate(['plan_id' => $plan2->id, 'currency_id' => $inr->id], ['monthly_price' => 299.00, 'yearly_price' => 2990.00]);
        PlanPrice::updateOrCreate(['plan_id' => $plan2->id, 'currency_id' => $npr->id], ['monthly_price' => 480.00, 'yearly_price' => 4800.00]);

        // --- 3. Enterprise ---
        $plan3 = Plan::updateOrCreate(
            ['slug' => 'enterprise'],
            [
                'name' => 'Enterprise',
                'max_branches' => 20,
                'trial_days' => 60,
                'features' => ['Up to 20 Branches', 'Inventory Management', 'Advanced Analytics', 'Priority Support'],
                'is_active' => true,
            ]
        );
        $plan3->services()->sync([
            $services['inventory']->id,
            $services['menu-management']->id,
            $services['reporting']->id,
            $services['priority-support']->id,
            $services['advanced-analytics']->id
        ]);

        // Plan 3 Prices
        PlanPrice::updateOrCreate(['plan_id' => $plan3->id, 'currency_id' => $inr->id], ['monthly_price' => 999.00, 'yearly_price' => 9990.00]);
        PlanPrice::updateOrCreate(['plan_id' => $plan3->id, 'currency_id' => $npr->id], ['monthly_price' => 1600.00, 'yearly_price' => 16000.00]);

        $this->command->info('Bhai, Plans and Multi-Country Prices successfully seed ho gaye! 🚀');
    }
}
