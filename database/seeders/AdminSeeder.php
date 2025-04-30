<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::updateOrCreate(
            ['email' => 'admin@example.com'],   // ← unique key
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('12345678'), // change to a strong pwd
            ]
        );
    }
}
