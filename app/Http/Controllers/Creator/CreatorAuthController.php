<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\CreatorResource;
use App\Models\Cat;
use App\Models\Creator;
use App\Services\SendCreatorOTPSerivce;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
                    $errorMsg = trans('disabledAccount');
                    return $this->sendResponse(false, null, $errorMsg, [['mobile' => $errorMsg]]);
                }

                $diff_otp_time = Carbon::now()->diffInSeconds(Carbon::parse($creator->otp_created_at));

                if ($diff_otp_time < 60) {
                    $errorMsg = trans('tryAgainAfter', ['seconds' => (60 - $diff_otp_time)]);
                    return $this->sendResponse(false, ['delay_seconds' => (60 - $diff_otp_time)], $errorMsg, [['mobile' => $errorMsg]]);
                }
            }

            $sendCreatorOTPSerivce = new SendCreatorOTPSerivce($creator);
            return $this->sendResponse(true, [
                'otp' => $sendCreatorOTPSerivce->otp,
            ], trans('otpHasBeenSentToYourMobile'), null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'));
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
                $errorMsg = trans('invalidOtp');
                return $this->sendResponse(false, null, $errorMsg, [['otp' => $errorMsg]]);
            }

            $diff_otp_time = Carbon::now()->diffInSeconds(Carbon::parse($creator->otp_created_at));
            if ($diff_otp_time > 600) {
                $errorMsg = trans('otpHasBeenExpired!');
                return $this->sendResponse(false, null, $errorMsg, [['mobile' => $errorMsg]]);
            }

            $creator->update(['otp' => '']);
            $creator->tokens()->delete();
            $token = $creator->createToken("auth_token");

            return $this->sendResponse(true, [
                'token' => $token->plainTextToken,
                'item' => new  CreatorResource($creator),
            ], 'Successfull login', null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'));
        }
    }

    function updateProfile(Request $request, $id)
    {
        try {
            $validator = Validator::make([...$request->all(), 'id' => $id], [
                'id' => 'required|exists:creators,creator_id',
                'creator_name' => 'required|min:3|max:60|unique:creators,creator_name,' . $id . ',creator_id',
                'logo' => 'required|min:6|max:190',
                'bio' => 'required|min:10|max:200',
                'address' => 'required|min:10|max:190',
                'brith_date' => 'required|date|after:1925-07-11|before:' . Carbon::now()->subYears(10),
                'IBAN_no' => 'nullable|regex:/^SA\d{22}$/|size:24',
                'Mawthooq_no' => 'nullable|min:5|max:20',
                'cat_names' => 'required|array',
                'cat_names.*' => 'required|exists:cats,cat_name',
                'sampleVideos' => 'required|array',
                'sampleVideos.*.video_url' => 'required|url|min:3|max:190',
                'sampleVideos.*.video_order_no' => 'required|numeric|min:0|max:10000',
                'sampleVideos.*.video_image_path' => 'required|min:10|max:190',
                'sampleVideos.*.video_description' => 'nullable|min:10|max:200',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $creator = Creator::findOrFail($id);

            DB::beginTransaction();
            $creator->update([
                'creator_name' => $request->creator_name ?? $creator->creator_name,
                'logo' => $request->logo ?? $creator->logo,
                'bio' => $request->bio ?? $creator->bio,
                'address' => $request->address ?? $creator->address,
                'brith_date' => $request->brith_date ?? $creator->brith_date,
                'IBAN_no' => $request->IBAN_no ?? $creator->IBAN_no,
                'Mawthooq_no' => $request->Mawthooq_no ?? $creator->Mawthooq_no,
            ]);



            $creator->sampleVideos()->delete();
            $creator->sampleVideos()->createMany($request->sampleVideos);



            $cat_ids = Cat::withTrashed()->whereIn('cat_name', $request->cat_names)->pluck('cat_id')->toArray();

            $creator->cats()->sync($cat_ids);
            DB::commit();

            return $this->sendResponse(true, [
                'item' => new  CreatorResource($creator),
            ], trans('successfullLogin'), null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'));
        }
    }
}
