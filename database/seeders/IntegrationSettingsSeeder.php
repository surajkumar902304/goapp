<?php

namespace Database\Seeders;

use App\Models\IntegrationSetting;
use Illuminate\Database\Seeder;

class IntegrationSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        IntegrationSetting::updateOrCreate(
            ['provider' => 'sendcloud'], 
            [
                'public_key' => 'update public key',
                'secret_key' => 'update secret key',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }
}
