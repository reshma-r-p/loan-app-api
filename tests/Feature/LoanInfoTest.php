<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\LoanInfo;

class LoanInfoTest extends TestCase
{
    use RefreshDatabase;

    public function testRAuthenticatedUserCanCreateLoanRequest()
    {

        $this->json('POST', 'api/loan-info', ['Accept' => 'application/json'])
            ->assertStatus(401);
    }

    public function testRequiredFieldsForLoanRequestCreation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $this->json('POST', 'api/loan-info', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "success"=> false,
                "message"=> [
                    "loan_type"=> [
                        "The loan type field is required."
                    ],
                    "amount"=> [
                        "The amount field is required."
                    ],
                    "term"=> [
                        "The term field is required."
                    ],
                    "is_submit"=> [
                        "The is submit field is required."
                    ]

                ]
            ]);
    }

    public function testSuccessfulLoanRequest()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $loanData = [
            "borrower_id" => $user->id,
            "loan_type" => "Personal",
            "loan_account" => "LA0000TEST".LoanInfo::latest('id')->value('id'),
            "amount" => 10000,
            "term" => 5,
            "is_submit" => true,

        ];

        $this->json('POST', 'api/loan-info', $loanData, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJsonStructure([
                    "success",
                    "data"=>[
                        "loan_info"=> [
                            "id",
                            "borrower_id",
                            "loan_account",
                            "loan_type",
                            "amount",
                            "term",
                            "repayment_type",
                            "interest_rate",
                            "reason_for_loan",
                            "status",
                            "created_at",
                            "updated_at"
                        ]

                    ],
                    "message"
            ]);
    }
}