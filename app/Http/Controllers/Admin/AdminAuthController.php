<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Http\Traits\AuthApiHelper;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminAuthController  extends BaseApiController
{
    public function login(Request $request) {
        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required|exists:admins,email',
                'password' => 'required',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $item = Admin::where('email', $request->email)->first();
            if(!$item){
                return $this->sendResponse(false, null, trans('inActiveAccount'), ['email'=>[trans('inActiveAccount')]]);
            }

            $item->tokens()->delete();
            $token = $item->createToken("auth_token");

            return $this->sendResponse(true, [
                'token' => $token->plainTextToken,
                'item' => new  AdminResource($item),
            ], 'Successfull login', null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'));
        }
    }
}
