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
            'balance' => '0',
            'address' => '123 Main St',
        ]);
    }
}
