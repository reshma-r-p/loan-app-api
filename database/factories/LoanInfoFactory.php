<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\LoanInfo;

class LoanInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
   
        return [
            "borrower_id" => User::factory(),
            "loan_type" => "Personal",
            "loan_account" => "LA0000TEST".LoanInfo::latest('id')->value('id'),
            "amount" => 10000,
            "term" => 5,
            "repayment_type" => "weekly",
            "interest_rate" => 10.01,
            "loan_account" => "LA000".LoanInfo::latest('id')->value('id'),
            "status" => "pending",
        ];
    }
}
