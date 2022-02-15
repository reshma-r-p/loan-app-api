<?php

namespace App\Http\Controllers\API;

use App\Models\LoanInfo;
use App\Http\Helpers\ApiHelpers;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Validator;

class LoanInfoController extends BaseController
{
    use ApiHelpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $loanInfo = LoanInfo::with('liability')->orderBy('id','desc');
        if(Auth::user()->role == 'borrower') {
            $loanInfo->where('borrower_id',Auth::id());
        }
        if($request->borrower_id && Auth::user()->role == 'lender')
            $loanInfo->where('borrower_id',$request->borrower_id);
        if($request->loan_account)
            $loanInfo->where('loan_account',$request->loan_account);
        $response = $loanInfo->paginate();
        
        return $this->handleResponse($response, 'Loan/loans retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'borrower_id' => 'nullable|exists:users,id',
            'loan_type' => 'required',
            'amount' => 'required',
            'term' => 'required|integer',
            'is_submit'  => 'required|boolean',
        ]);
   
        if($validator->fails()){
            return $this->handleError($validator->errors(),[],422);       
        }

        $input = $request->all();
        $input['borrower_id'] = $input['borrower_id'] ?? Auth::id(); 
        $input['repayment_type'] = 'weekly';
        $input['interest_rate'] = '10.01';
        $input['loan_account'] = 'LA000'.LoanInfo::latest('id')->value('id');
        $input['status'] = $input['is_submit'] ? 'pending':'draft';

        $loan = LoanInfo::create($input);
        $success['loan_info'] =  $loan->fresh();
   
        return $this->handleResponse($success, 'Loan request created!',201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Models\LoanInfo  $loanInfo
     * @return \Illuminate\Http\Response
     */
    public function show(LoanInfo $loanInfo)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Models\LoanInfo  $loanInfo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LoanInfo $loanInfo)
    {
        $validator = Validator::make($request->all(), [
            'loan_type' => 'required',
            'amount' => 'required',
            'term' => 'required|integer',
            'is_submit'  => 'required|boolean',
        ]);
   
        if($validator->fails()){
            return $this->handleError($validator->errors());       
        }
        if($loanInfo->borrower_id == Auth::id() || $this->isLenderOrAdmin($request->user())) {
            if($loanInfo->status != 'draft')
            return $this->handleError('Invalid Request.', ['error'=>'You cant edit already submitted form !'], 500);
        
            $loanInfo->loan_type = $request->loan_type;
            $loanInfo->amount = $request->amount;
            $loanInfo->term = $request->term;
            $loanInfo->reason_for_loan = $request->reason_for_loan;
            $loanInfo->save();
            
            $success['loan_info'] =  $loanInfo->fresh();
       
            return $this->handleResponse($success, 'Loan request updated!');
        }
        return $this->handleError('Unauthorised.', ['error'=>'You dont have permission to perform this operation !'], 401);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Models\LoanInfo  $loanInfo
     * @return \Illuminate\Http\Response
     */
    public function destroy(LoanInfo $loanInfo)
    {
        if($loanInfo->status != 'draft')
            return $this->handleError('Invalid Request.', ['error'=>'You cant edit already submitted form !'], 500);

        $loanInfo->delete();
   
        return $this->handleResponse([], 'Loan request deleted successfully.');
    }
}
