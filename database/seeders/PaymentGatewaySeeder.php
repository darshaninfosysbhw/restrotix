<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentGateway;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DatabaseSeeder.php ya PaymentGatewaySeeder.php mein:
        PaymentGateway::create([
            'name' => 'Khalti',
            'slug' => 'khalti',
            'credentials' => [
                'public_key' => 'your_test_public_key',
                'secret_key' => 'your_test_secret_key',
            ],
            'supported_currencies' => ['NPR'],
            'mode' => 'sandbox',
            'is_active' => true,
        ]);

        PaymentGateway::create([
            'name' => 'Stripe',
            'slug' => 'stripe',
            'credentials' => [
                'publishable_key' => 'pk_test_...',
                'secret_key' => 'sk_test_...',
            ],
            'supported_currencies' => ['USD', 'INR'],
            'mode' => 'sandbox',
            'is_active' => true,
        ]);
    }
}
