<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class MinOrderDelivery extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::updateOrCreate(
        ['key'   => 'min_order_free_delivery'],
            ['value' => '500',]
        );

        Setting::updateOrCreate(
            ['key' => 'min_order_place'],
            ['value' => '200'] 
        );
    }
}
