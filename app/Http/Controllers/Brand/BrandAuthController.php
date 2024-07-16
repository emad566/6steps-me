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

    function updateProfile(Request $request, $id)
    {
        try {
            if (auth()->user()->brand_id != $id && !auth('admin')->check()) {
                return $this->sendResponse(false, [], "You are not admin user", null, 400);
            }

            $validator = Validator::make([...$request->all(), 'id' => $id], [
                'id' => 'required|exists:brands,brand_id',
                'brand_name' => 'required|min:3|max:60|unique:brands,brand_name,' . $id . ',brand_id',
                'logo' => 'required|min:5|max:190',
                'website_url' => 'nullable|url|min:5|max:190',
                'description' => 'required|min:5|max:200',
                'address' => 'required|min:5|max:190',
                'branches_no' => 'required|numeric|min:0|max:2000',
                'tax_no' => 'required|min:15|max:15',
                'cr_no' => 'required|min:10|max:10',
                'cat_names' => 'required|array',
                'cat_names.*' => 'required|exists:cats,cat_name',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $item = Brand::findOrFail($id);

            DB::beginTransaction();
            $item->update([
                'brand_name' => $request->brand_name,
                'logo' => $request->logo,
                'website_url' => $request->website_url,
                'description' => $request->description,
                'address' => $request->address,
                'branches_no' => $request->branches_no,
                'tax_no' => $request->tax_no,
                'cr_no' => $request->cr_no,
            ]);

            $cat_ids = Cat::withTrashed()->whereIn('cat_name', $request->cat_names)->pluck('cat_id')->toArray();
            $item->cats()->sync($cat_ids);
            DB::commit();

            return $this->sendResponse(true, [
                'item' => new  BrandResource($item),
            ], trans('successfullLogin'), null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }
}
