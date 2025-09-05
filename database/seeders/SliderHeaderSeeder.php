<?php

namespace Database\Seeders;

use App\Models\SliderHeader;
use Illuminate\Database\Seeder;

class SliderHeaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $headers = [
            ['header_name' => 'first banner slider', 'header_value' => 'EXPLORE THE DEALS CENTRE'],
            ['header_name' => 'first product slider', 'header_value' => 'NEW PRODUCTS'],
            ['header_name' => 'second banner slider', 'header_value' => 'FRUITS'],
            ['header_name' => 'second product slider', 'header_value' => 'TOP SELLER']
        ];

        foreach ($headers as $data) {
            SliderHeader::firstOrCreate(
                ['header_name' => $data['header_name']],
                ['header_value' => $data['header_value']]
            );
        }
    }
}
