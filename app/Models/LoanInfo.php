<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanInfo extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'borrower_id',
        'loan_account',
        'loan_type',
        'amount',
        'term',
        'repayment_type',
        'interest_rate',
        'reason_for_loan',
        'status'
    ];

    public function liability()
    {
        return $this->hasOne(LoanLiability::Class);
    }

    public function repayment()
    {
        return $this->hasMany(RepaymentInfo::Class);
    }
}
