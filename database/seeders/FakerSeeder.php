<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class FakerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 0; $i < 10; $i++) {
            \App\Models\User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'phone_number' => $faker->unique()->phoneNumber,
                'pin' => $faker->numberBetween(100000, 999999),
                'balance' => $faker->numberBetween(0, 9999999),
                'address' => $faker->address,
                'token' => $faker->unique()->uuid,
            ]);
        }
    }
}
