<?php

namespace Database\Seeders;

use App\Models\StripeIntegration;
use Illuminate\Database\Seeder;

class StripeIntegrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StripeIntegration::updateOrCreate(
            ['provider' => 'stripe'], 
            [
                'publishable_key' => 'update publishable key',
                'secret_key' => 'update secret key',
                'webhook_secret' => 'update webhook secret key',
                'test_mode' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }
}
