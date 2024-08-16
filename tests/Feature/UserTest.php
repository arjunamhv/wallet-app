<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\UserSeeder;
use App\Models\User;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testRegisterSuccess()
    {
        $this->post('/api/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone_number' => '081234567890',
            'pin' => '123456',
            'address' => '123 Main St',
        ])->assertStatus(201)
            ->assertJson([
                "status" => "SUCCESS",
                "result" => [
                    "first_name" => "John",
                    "last_name" => "Doe",
                    "phone_number" => "081234567890",
                    "pin" => "123456",
                    "balance" => "0",
                    "address" => "123 Main St"
                ]
            ]);
    }

    public function testRegisterFailedValidation()
    {
        $this->post('/api/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone_number' => '081234567890',
            'pin' => '123',
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'pin' => [
                        'The PIN field must be at least 6 characters.'
                    ],
                ],
            ]);
    }
    public function testRegisterPhoneExist()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone_number' => '081234567890',
            'pin' => '123456',
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'phone_number' => [
                        'The Phone Number field has already been taken.'
                    ],
                ],
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/login', [
            'phone_number' => '081234567890',
            'pin' => '123456',
        ])->assertStatus(201)
            ->assertJsonStructure([
                "status",
                "result" => [
                    "token"
                ]
            ]);
        $user = User::where('phone_number', '081234567890')->first();
        self::assertNotNull($user->token);
    }

    public function testLoginFailedValidation()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/login', [
            'phone_number' => '081234567432',
            'pin' => '123',
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'pin' => [
                        'The PIN field must be at least 6 characters.'
                    ],
                ],
            ]);
    }

    public function testLoginInvalidPhoneNumber()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/login', [
            'phone_number' => '081234567432',
            'pin' => '123456',
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Invalid phone number or pin'
                    ]
                ]
            ]);
    }

    public function testLoginInvalidPin()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/login', [
            'phone_number' => '081234567890',
            'pin' => '123455',
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Invalid phone number or pin'
                    ]
                ]
            ]);
    }

    public function testGetUserSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/user', [
            'Authorization' => 'janedoe_token'
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    "first_name" => "Jane",
                    "last_name" => "Doe",
                    "phone_number" => "081234567891",
                    "pin" => "123456",
                    "balance" => "0",
                    "address" => "123 Main St"
                ]
            ]);
    }
    public function testGetUserUnauthorized(): void
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/user')
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }
    public function testGetUserInvalidToken(): void
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/user', [
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

    public function testUpdateUserSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('phone_number', '081234567891')->first();
        $this->patch('/api/user', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'pin' => '654321',
            'address' => '123 Main St'
        ], [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(200)
            ->assertJson([
                "status" => "SUCCESS",
                "result" => [
                    "first_name" => "Jane",
                    "last_name" => "Doe",
                    "phone_number" => "081234567891",
                    "pin" => "654321",
                    "balance" => "0",
                    "address" => "123 Main St"
                ]
            ]);

        $newUser = User::where('phone_number', '081234567891')->first();
        self::assertNotEquals($oldUser->pin, $newUser->pin);
    }
    public function testUpdateUserUnauthorized(): void
    {
        $this->seed([UserSeeder::class]);
        $this->patch('/api/user', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'pin' => '654321',
            'address' => '123 Main St'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }
    public function testUpdateUserFailedValidation(): void
    {
        $this->seed([UserSeeder::class]);
        $this->patch('/api/user', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'pin' => '123',
            'address' => '123 Main St'
        ], [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'pin' => [
                        'The PIN field must be at least 6 characters.'
                    ]
                ]
            ]);
    }
}
