<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\UserSeeder;
use App\Models\User;
use App\Models\Topup;



class TopupTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testTopupSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/topup', [
            'amount' => 200000
        ], [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "result" => [
                    "id",
                    "user_id",
                    "amount",
                    "balance_before",
                    "balance_after",
                ],
            ]);
        $user = User::where('phone_number', '081234567891')->first();
        self::assertEquals(200000, $user->balance);

        $topup = Topup::where('user_id', $user->id)->latest()->firstOrFail();
        self::assertEquals(0, $topup->balance_before);
        self::assertEquals(200000, $topup->balance_after);
    }
    public function testTopupUnauthorized(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/topup', [
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
    public function testTopupBadRequest(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/topup', [
            'amount' => "string"
        ], [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'amount' => [
                        'The amount field must be a number.',
                    ]
                ],
            ]);
    }
}
