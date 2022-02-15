<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\LoanInfo;
use App\Models\LoanLiability;

class RepaymentTest extends TestCase
{
    use RefreshDatabase;

    public function testRAuthenticatedUserCanCreateRepayment()
    {

        $this->json('POST', 'api/repayment', ['Accept' => 'application/json'])
            ->assertStatus(401);
    }

    public function testRequiredFieldsForRepaymentCreation()
    {
       $user = User::factory()->create();
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $this->json('POST', 'api/repayment', ['Accept' => 'application/json'])
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
                    "transaction_id" => [
                        "The transaction id field is required."
                    ],
                    "total_amount_paid" => [
                        "The total amount paid field is required."
                    ],
                    "loan_amount_paid" => [
                        "The loan amount paid field is required."
                    ],
                    "fine" => [
                        "The fine field is required."
                    ],
                    "payment_date" => [
                        "The payment date field is required."
                    ]

                ]
            ]);
    }

    public function testSuccessfulRepayment()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $loanInfo = LoanInfo::factory($user)->create();
        $loanInfo->status = 'approved';
        $liability = new LoanLiability();
        $liability->disbursed_amount = 100000;
        $liability->disbursed_date = '2022-02-08';
        $liability->outstanding_amount = 100000;
        $liability->repayment_amount = 1200;
        $liability->liability_start_date = '2022-03-08';
        $liability->liability_end_date = '2027-02-08';
        $loanInfo->liability()->save($liability);
        $loanInfo->save();


        $repayData = [
            'loan_info_id' => $loanInfo->id,
            'status' => 'paid',
            'transaction_id' => 'LATR000001',
            'total_amount_paid' => 1500,
            'loan_amount_paid' => 1400,
            'fine' => 100,
            'payment_date' =>'2022-03-08'

        ];

        $this->json('POST', 'api/repayment', $repayData, ['Accept' => 'application/json'])
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
                            "updated_at",
                            "repayment"
                        ]

                    ],
                    "message"
            ]);
    }
}