<?php

namespace Tests\Feature;

use Tests\TestCase;
use Database\Seeders\UserSeeder;
use App\Models\User;
use App\Models\Transfer;


class TransferTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testTransferSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $targetUser = User::where('phone_number', '081234567891')->first();
        $this->post('/api/transfer', [
            'target_user_id' => $targetUser->id,
            'amount' => 150000,
            'remarks' => 'birthday gift'

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

        $topup = transfer::where('user_id', $user->id)->latest()->firstOrFail();
        self::assertEquals(200000, $topup->balance_before);
        self::assertEquals(50000, $topup->balance_after);
    }

    public function testTransferInsufficientBalance(): void
    {
        $this->seed([UserSeeder::class]);
        $targetUser = User::where('phone_number', '081234567891')->first();
        $user = User::where('phone_number', '081234567892')->first();
        $balance_before = $user->balance;
        $this->post('/api/transfer', [
            'target_user_id' => $targetUser->id,
            'amount' => 300000,
            'remarks' => 'birthday gift'
        ], [
            'Authorization' => 'marryjane_token'
        ])->assertStatus(400)
            ->assertJson([
                "message" => 'Insufficient balance for this transfer.'
            ]);
        $user->refresh();
        $balance_after = $user->balance;
        self::assertEquals($balance_before, $balance_after);
    }

    public function testTransferRecipientNotFound(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('phone_number', '081234567892')->first();
        $balance_before = $user->balance;
        $this->post('/api/transfer', [
            'target_user_id' => "invalid target user id",
            'amount' => 150000,
            'remarks' => 'birthday gift'
        ], [
            'Authorization' => 'marryjane_token'
        ])->assertStatus(400)
            ->assertJson([
                "message" => 'Recipient not found.'
            ]);
        $user->refresh();
        $balance_after = $user->balance;
        self::assertEquals($balance_before, $balance_after);
    }

    public function testTransferUnauthorized(): void
    {
        $this->seed([UserSeeder::class]);
        $targetUser = User::where('phone_number', '081234567891')->first();
        $this->post('/api/transfer', [
            'target_user_id' => $targetUser->id,
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

    public function testTransferBadRequest(): void
    {
        $this->seed([UserSeeder::class]);
        $targetUser = User::where('phone_number', '081234567891')->first();
        $this->post('/api/transfer', [
            'target_user_id' => $targetUser->id,
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
