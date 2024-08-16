<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Topup;
use App\Models\Payment;
use App\Models\Transfer;
use Faker\Factory as Faker;


class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $user = User::where('phone_number', '081234567891')->first();

        for ($i = 0; $i < 2; $i++) {
            $amount = $faker->numberBetween(5000000, 9999999);
            $balanceBefore = $user->balance;
            $user->balance += $amount;

            Topup::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $user->balance,
            ]);

            $user->save();
        }

        for ($i = 0; $i < 2; $i++) {
            $amount = $faker->numberBetween(0, 999999);
            $balanceBefore = $user->balance;
            $user->balance -= $amount;

            Payment::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'remarks' => $faker->sentence,
                'balance_before' => $balanceBefore,
                'balance_after' => $user->balance,
            ]);

            $user->save();
        }

        for ($i = 0; $i < 2; $i++) {
            $amount = $faker->numberBetween(0, 999999);
            $balanceBefore = $user->balance;
            $user->balance -= $amount;

            Transfer::create([
                'user_id' => $user->id,
                'target_user_id' => $faker->randomElement(User::pluck('id')->toArray()),
                'amount' => $amount,
                'remarks' => $faker->sentence,
                'balance_before' => $balanceBefore,
                'balance_after' => $user->balance,
            ]);

            $user->save();
        }
    }
}
