<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\LoanInfo;

class IssuerTest extends TestCase
{
    use RefreshDatabase;

    public function testRAuthenticatedUserCanProcessLoanRequest()
    {

        $this->json('POST', 'api/process-loan', ['Accept' => 'application/json'])
            ->assertStatus(401);
    }

    public function testRequiredFieldsForProcessLoanRequest()
    {
        $user = User::where('role','lender')->first();
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $this->json('POST', 'api/process-loan', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "success"=> false,
                "message"=> [
                    "loan_info_id" => [
                        "The loan info id field is required."
                    ],
                    "status" => [
                        "The status field is required."
                    ],
                    "disbursed_amount" => [
                        "The disbursed amount field is required."
                    ],
                    "disbursed_date" => [
                        "The disbursed date field is required."
                    ],
                    "outstanding_amount" => [
                        "The outstanding amount field is required."
                    ],
                    "repayment_amount" => [
                        "The repayment amount field is required."
                    ],
                    "liability_start_date" => [
                        "The liability start date field is required."
                    ],
                    "liability_end_date" => [
                        "The liability end date field is required."
                    ]

                ]
            ]);
    }

    public function testSuccessfulProcessLoanRequest()
    {
        $user = User::where('role','lender')->first();
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $loan = LoanInfo::factory()->create();

        $loanData = [
            "loan_info_id" => $loan->id,
            "status" => 'approved',
            "disbursed_amount" => 100000,
            "disbursed_date" => '2022-02-08',
            "outstanding_amount" => 100000,
            "repayment_amount" => 1200,
            "liability_start_date" => '2022-03-08',
            "liability_end_date" => '2027-02-08'

        ];

        $this->json('POST', 'api/process-loan', $loanData, ['Accept' => 'application/json'])
            ->assertStatus(200)
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