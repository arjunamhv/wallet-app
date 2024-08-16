<?php

namespace Tests\Feature;

use Tests\TestCase;
use Database\Seeders\UserSeeder;
use Database\Seeders\TransactionSeeder;

class TransactionTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testTransactionReportSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $this->seed([TransactionSeeder::class]);
        $response = $this->get('/api/transaction', [
            'Authorization' => 'janedoe_token'
        ])
            ->assertStatus(200)
            ->assertJsonStructure([
                "status",
                'result' => [
                    '*' => [
                        'id',
                        'user_id',
                        'amount',
                        'balance_before',
                        'balance_after',
                        'type',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]);
            $responseData = $response->json('result');
            $this->assertCount(6, $responseData);
    }
}
