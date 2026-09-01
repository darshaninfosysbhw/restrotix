<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CurrencySeeder::class,
            CountrySeeder::class,
            PaymentGatewaySeeder::class,
            PlanSeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}
