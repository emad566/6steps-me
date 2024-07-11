<?php

namespace App\Http\Controllers\Creator\Auth;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\CreatorResource;
use App\Models\Creator;
use App\Services\SendCreatorOTPSerivce;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;

class CreatorAuthController extends BaseApiController
{
    public function loginRegisterResendOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mobile' => 'required|sa_mobile',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $creator = Creator::withTrashed()->where('mobile', $request->mobile)->first();
            if (!$creator) {
                $creator = Creator::create(['mobile' => $request->mobile]);
            } else {
                if ($creator->deleted_at) {
                    $errorMsg = 'The account is disabled. Please contact us to solve the problem';
                    return $this->sendResponse(false, [], $errorMsg, [['mobile' => $errorMsg]]);
                }

                $diff_otp_time = Carbon::now()->diffInSeconds(Carbon::parse($creator->otp_created_at));

                if ($diff_otp_time < 60) {
                    $errorMsg = 'Please wait ' . (60 - $diff_otp_time) . ' seconds then try again';
                    return $this->sendResponse(false, [], $errorMsg, [['mobile' => $errorMsg]]);
                }
            }

            $sendCreatorOTPSerivce = new SendCreatorOTPSerivce($creator);
            return $this->sendResponse(true, ['otp' => $sendCreatorOTPSerivce->otp], 'OTP has been sent to your mobile', []);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, [], "Technical Error!");
        }
    }

    public function otpVerify(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mobile' => 'required|sa_mobile|exists:creators,mobile',
                'otp' => 'required|exists:creators,otp'
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $creator = Creator::withTrashed()->where('mobile', $request->mobile)
                ->where('otp', $request->otp)
                ->first();

            if (!$creator) {
                $errorMsg = 'Invalid OTP';
                return $this->sendResponse(false, [], $errorMsg, [['otp' => $errorMsg]]);
            }

            $diff_otp_time = Carbon::now()->diffInSeconds(Carbon::parse($creator->otp_created_at));

            if ($diff_otp_time > 12000) {
                $errorMsg = 'OTP has been expired!';
                return $this->sendResponse(false, [], $errorMsg, [['mobile' => $errorMsg]]);
            }

            $creator->update(['otp' => '']);
            $token = $creator->createToken("auth_token");

            return $this->sendResponse(true, [
                'token' => $token->plainTextToken,
                'item' => new  CreatorResource($creator),
            ], 'Successfull login', []);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, [], "Technical Error!");
        }
    }

    function updateProfile(Request $request, $id)
    {
        try{
            $validator = Validator::make([...$request->all(), 'id' => $id], [
                'id' => 'required|exists:creators,creator_id',
                'creator_name' => 'required|min:3|max:60',
                'logo' => 'required|min:6|max:190',
                'bio' => 'required|min:10|max:200',
                'address' => 'required|min:10|max:190',
                'brith_date' => 'required|date|after:1925-07-11|before:' . Carbon::now()->subYears(10),
                'IBAN_no' => 'nullable|regex:/^SA\d{22}$/|size:24',
                'Mawthooq_no' => 'nullable|min:5|max:20',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $creator = Creator::findOrFail($id);
            $creator->update([
                'creator_name' => $request->creator_name ?? $creator->creator_name,
                'logo' => $request->logo ?? $creator->logo,
                'bio' => $request->bio ?? $creator->bio,
                'address' => $request->address ?? $creator->address,
                'brith_date' => "1990-11-15",
                'IBAN_no' => $request->IBAN_no ?? $creator->IBAN_no,
                'Mawthooq_no' => $request->Mawthooq_no ?? $creator->Mawthooq_no,
            ]);

            return $this->sendResponse(true, [
                'item' => new  CreatorResource($creator),
            ], 'Successfull login', []);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, [], "Technical Error!");
        }
    }
}
