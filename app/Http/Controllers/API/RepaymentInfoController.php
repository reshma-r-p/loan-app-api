<?php

namespace App\Http\Controllers\API;

use App\Models\RepaymentInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\LoanInfo;
use App\Http\Helpers\ApiHelpers;
use Illuminate\Support\Facades\Auth;
use Validator;

class RepaymentInfoController extends BaseController
{
    use ApiHelpers;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, LoanInfo $loanInfo)
    {
        $validator = Validator::make($request->all(), [
                'loan_info_id' => 'required|exists:loan_infos,id',
            ]);

            if($validator->fails()){
                return $this->handleError($validator->errors(),[],422);       
        }  
        $repayments = RepaymentInfo::whereLoanInfoId($request->loan_info_id)->get();
        $success['repayments'] =  $repayments;
   
        return $this->handleResponse($success, 'Repayments listed!');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

            // assuming that payment is doing from mobile side and details will be passing back.
            $validator = Validator::make($request->all(), [
                'loan_info_id' => 'required|exists:loan_infos,id',
                'status' => 'required',
                'transaction_id' => 'required',
                'total_amount_paid' => 'required',
                'loan_amount_paid' => 'required',
                'fine' => 'required',
                'payment_date' => 'required'
                
            ]);

            if($validator->fails()){
                return $this->handleError($validator->errors(),[],422);       
        }  
        $loanInfo = LoanInfo::find($request->loan_info_id);

        $loanInfo->liability->outstanding_amount = $request->outstanding_amount - $request->loan_amount_paid;

        $repayment = new RepaymentInfo();
        $repayment->transaction_id = $request->transaction_id;
        $repayment->total_amount_paid = $request->total_amount_paid;
        $repayment->loan_amount_paid = $request->loan_amount_paid;
        $repayment->fine = $request->fine;
        $repayment->payment_date = $request->payment_date;
        $repayment->status = $request->status;

        $loanInfo->repayment()->save($repayment);
        $loanInfo->save();
        $success['loan_info'] =  $loanInfo->load('repayment');
   
        return $this->handleResponse($success, 'Repayment info added!');
            

    }

    
}
