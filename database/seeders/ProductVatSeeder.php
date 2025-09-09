<?php

namespace Database\Seeders;

use App\Models\ProductVat;
use Illuminate\Database\Seeder;

class ProductVatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductVat::updateOrCreate(
            ['product_vat'   => '20'],
        );
    }
}
