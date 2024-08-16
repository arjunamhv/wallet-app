<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\UserSeeder;
use App\Models\User;
use App\Models\Payment;


class PaymentTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testPaymentSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/payment', [
            'amount' => 150000,
            'remarks' => 'monthly groceries'

        ], [
            'Authorization' => 'marryjane_token'
        ])->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "result" => [
                    "id",
                    "user_id",
                    "amount",
                    "remarks",
                    "balance_before",
                    "balance_after",
                ],
            ]);
        $user = User::where('phone_number', '081234567892')->first();
        self::assertEquals(50000, $user->balance);

        $topup = Payment::where('user_id', $user->id)->latest()->firstOrFail();
        self::assertEquals(200000, $topup->balance_before);
        self::assertEquals(50000, $topup->balance_after);
    }

    public function testPaymentInsufficientBalance(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('phone_number', '081234567892')->first();
        $balance_before = $user->balance;
        $this->post('/api/payment', [
            'amount' => 300000,
            'remarks' => 'monthly groceries'
        ], [
            'Authorization' => 'marryjane_token'
        ])->assertStatus(400)
            ->assertJson([
                "message" => 'Insufficient balance for this payment.'
            ]);
        $user->refresh();
        $balance_after = $user->balance;
        self::assertEquals($balance_before, $balance_after);
    }

    public function testPaymentUnauthorized(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/payment', [
            'amount' => 200000
        ], [
            'Authorization' => 'invalid_token'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

    public function testpaymentBadRequest(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/payment', [
            'amount' => 200000
        ], [
            'Authorization' => 'marryjane_token'
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'remarks' => [
                        'The remarks field is required.',
                    ]
                ],
            ]);
    }
}
