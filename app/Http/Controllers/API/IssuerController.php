<?php

namespace App\Http\Controllers\API;

use App\Models\LoanInfo;
use App\Models\LoanLiability;
use App\Http\Helpers\ApiHelpers;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Validator;

class IssuerController extends BaseController
{
    use ApiHelpers;

    public function processLoanRequest(Request $request) {

        if ($this->isLenderOrAdmin($request->user())) {

            $validator = Validator::make($request->all(), [
                'loan_info_id' => 'required|exists:loan_infos,id',
                'status' => 'required',
                'disbursed_amount' => 'required',
                'disbursed_date' => 'required',
                'outstanding_amount' => 'required',
                'repayment_amount' => 'required',
                'liability_start_date' => 'required',
                'liability_end_date' => 'required',
            ]);

            if($validator->fails()){
                return $this->handleError($validator->errors(),[],422);       
            } 


            $loanInfo = LoanInfo::with('liability')->find($request->loan_info_id);

            if($loanInfo->status !== "pending")
                return $this->handleError('Request alreday processed.', ['error'=>'Alreday processed request'], 400);

            $loanInfo->status = $request->status;
            $loanInfo->save();

            if($loanInfo->status == 'approved') {
                $liability = new LoanLiability();
                $liability->disbursed_amount = $request->disbursed_amount;
                $liability->disbursed_date = $request->disbursed_date;
                $liability->outstanding_amount = $request->outstanding_amount;
                $liability->repayment_amount = $request->repayment_amount;
                $liability->liability_start_date = $request->liability_start_date;
                $liability->liability_end_date = $request->liability_end_date;

                $loanInfo->liability()->save($liability);
                $loanInfo->save();
            }

            $success['loan_info'] =  $loanInfo->load('liability');
   
            return $this->handleResponse($success, 'Loan request status updated!');
 
        }

        return $this->handleError('Unauthorised.', ['error'=>'You dont have permission to perform this operation !'], 401);
    }
}
