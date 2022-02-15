<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class AuthController extends BaseController
{
     public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->handleError($validator->errors(),[],422);       
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $auth = Auth::user(); 
            $success['token'] =  $auth->createToken('auth_token', [$auth->role])->plainTextToken;
            $success['user'] =  $auth;
   
            return $this->handleResponse($success, 'User logged-in!',200);
        } 
        else{ 
            return $this->handleError('Unauthorised.', ['error'=>'Unauthorised'], 401);
        } 
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->handleError($validator->errors(),[],422);       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('auth_token', ['borrower'])->plainTextToken;
        $success['user'] =  $user->fresh();
   
        return $this->handleResponse($success, 'User successfully registered!', 201);
    }
}
