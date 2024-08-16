<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone_number' => '081234567890',
            'pin' => '123456',
            'balance' => 0,
            'address' => '123 Main St',
        ]);

        User::create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'phone_number' => '081234567891',
            'pin' => '123456',
            'balance' => 0,
            'address' => '123 Main St',
            'token' => 'janedoe_token'
        ]);

        User::create([
            'first_name' => 'marry',
            'last_name' => 'jane',
            'phone_number' => '081234567892',
            'pin' => '123456',
            'balance' => 200000,
            'address' => '123 Main St',
            'token' => 'marryjane_token'
        ]);
    }
}
