<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'Indian Rupee',
                'code' => 'INR',
                'symbol' => '₹',
                'exchange_rate' => 1.00, // बेस करेंसी मान लेते हैं
                'is_active' => true,
            ],
            [
                'name' => 'Nepalese Rupee',
                'code' => 'NPR',
                'symbol' => 'रू',
                'exchange_rate' => 1.60, // 1 INR = 1.6 NPR
                'is_active' => true,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }

        $this->command->info('Bhai, Currencies (INR & NPR) setup ho gayi hain! 🇳🇵🇮🇳');
    }
}
