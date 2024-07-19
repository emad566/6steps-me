<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\BrandResource;
use App\Models\AppConstants;
use App\Models\Brand;
use App\Models\Cat;
use App\Services\SendEmailOTPSerivce;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class BrandAuthController extends BaseApiController
{
    public function loginRegisterResendOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|min:7|max:50',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $item = Brand::withTrashed()->where('email', $request->email)->first();
            if (!$item) {
                $item = Brand::create(['email' => $request->email]);
            } else {
                if ($item->deleted_at) {
                    $errorMsg = trans('disabledAccount');
                    return $this->sendResponse(false, null, $errorMsg, [['email' => $errorMsg]]);
                }

                $diff_otp_time = Carbon::now()->diffInSeconds(Carbon::parse($item->otp_created_at));

                $otpDelay = AppConstants::$otpDelay;

                if ($diff_otp_time < $otpDelay) {
                    $errorMsg = trans('tryAgainAfter', ['seconds' => ($otpDelay - $diff_otp_time)]);
                    return $this->sendResponse(false, ['delay_seconds' => ($otpDelay - $diff_otp_time)], $errorMsg, [['email' => $errorMsg]], 400);
                }
            }

            try {
                $sendBrandOTPSerivce = new SendEmailOTPSerivce($item);
            } catch (\Throwable $th) {
                return $this->sendResponse(false, [], trans('NotValidEmail'), ['email' => [trans('NotValidEmail')]], 400);
            }

            return $this->sendResponse(true, [
                'otp' => $sendBrandOTPSerivce->otp,
            ], trans('otpHasBeenSentToYourEmail'), null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

    public function otpVerify(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:brands,email',
                'otp' => 'required|exists:brands,otp'
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $item = Brand::withTrashed()->where('email', $request->email)
                ->where('otp', $request->otp)
                ->first();

            if (!$item) {
                $errorMsg = trans('invalidOtp');
                return $this->sendResponse(false, null, $errorMsg, [['otp' => $errorMsg]], 400);
            }

            $diff_otp_time = Carbon::now()->diffInSeconds(Carbon::parse($item->otp_created_at));
            if ($diff_otp_time > 600) {
                $errorMsg = trans('otpHasBeenExpired!');
                return $this->sendResponse(false, null, $errorMsg, [['email' => $errorMsg]], 400);
            }

            $item->update(['otp' => '', 'email_verified_at' => Carbon::now()]);
            $item->tokens()->delete();
            $token = $item->createToken("auth_token");

            return $this->sendResponse(true, [
                'token' => $token->plainTextToken,
                'item' => new  BrandResource($item),
            ], 'Successfull login', null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    } 
}
