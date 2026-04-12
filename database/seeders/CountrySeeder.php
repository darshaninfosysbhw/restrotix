<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Currency;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        // पहले से मौजूद करेंसी फेच करते हैं
        $inr = Currency::where('code', 'INR')->first();
        $npr = Currency::where('code', 'NPR')->first();

        $countries = [
            [
                'name' => 'India',
                'iso_code' => 'IN',
                'phone_code' => '+91',
                'currency_id' => $inr->id,
                'timezone' => 'Asia/Kolkata',
                'is_active' => true,
            ],
            [
                'name' => 'Nepal',
                'iso_code' => 'NP',
                'phone_code' => '+977',
                'currency_id' => $npr->id,
                'timezone' => 'Asia/Kathmandu',
                'is_active' => true,
            ],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['iso_code' => $country['iso_code']],
                $country
            );
        }

        $this->command->info('Bhai, Countries (India & Nepal) bhi link ho gayi hain! 🚀');
    }
}
