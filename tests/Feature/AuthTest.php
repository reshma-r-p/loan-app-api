<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    public function testRequiredFieldsForRegistration()
    {
        $this->json('POST', 'api/register', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "success"=> false,
                "message"=> [
                    "first_name"=> [
                        "The first name field is required."
                    ],
                    "email"=> [
                        "The email field is required."
                    ],
                    "password"=> [
                        "The password field is required."
                    ],
                    "confirm_password"=> [
                        "The confirm password field is required."
                    ]
                ]
            ]);
    }

    public function testRepeatPassword()
    {
        $userData = [
            "first_name" => "John",
            "last_name" => "Doe",
            "email" => "doe@example.com",
            "password" => "Demo100%",
            "password" => "Demo100"
        ];

        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "success"=> false,
                "message"=> [
                    "confirm_password"=> [
                        "The confirm password field is required."
                    ]
                ]

            ]);
    }

    public function testSuccessfulRegistration()
    {
        $userData = [
            "first_name" => "John",
            "last_name" => "Doe",
            "email" => "doe@example.com",
            "password" => "Demo100%",
            "confirm_password" => "Demo100%",

        ];

        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJsonStructure([
                    "success",
                    "data"=>[
                        "token",
                        "user"=>[
                            "id",
                            "first_name",
                            "last_name",
                            "email",
                            "role",
                            "address",
                            "gender",
                            "dob",
                            "email_verified_at",
                            "created_at",
                            "updated_at"
                        ]
                    ],
                    "message"
            ]);
    }

    public function testMustEnterEmailAndPassword()
    {
        $this->json('POST', 'api/login')
            ->assertStatus(422)
            ->assertJson([
                "success"=> false,
                "message"=> [
                    "email"=> [
                        "The email field is required."
                    ],
                    "password"=> [
                        "The password field is required."
                    ]
                ]
            ]);
    }

    public function testSuccessfulLogin()
    {
        $user = User::factory()->count(1)->create();

        $loginData = ['email' => $user[0]->email, 'password' => "password"];

        $this->json('POST', 'api/login', $loginData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
               "success",
                    "data"=>[
                        "token",
                        "user"=>[
                            "id",
                            "first_name",
                            "last_name",
                            "email",
                            "role",
                            "address",
                            "gender",
                            "dob",
                            "email_verified_at",
                            "created_at",
                            "updated_at"
                        ]
                    ],
                    "message"
            ]);

        $this->assertAuthenticated();
    }
}
