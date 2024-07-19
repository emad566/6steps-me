<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\CreatorResource;
use App\Http\Traits\DistroyTrait;
use App\Http\Traits\EditTrait;
use App\Http\Traits\IndexTrait;
use App\Http\Traits\ShowTrait;
use App\Http\Traits\ToggleActiveTrait;
use App\Models\Cat;
use App\Models\City;
use App\Models\Creator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreatorController extends BaseApiController
{
    use IndexTrait, ShowTrait, EditTrait, DistroyTrait, ToggleActiveTrait;

    protected $table = 'creators';
    protected $model = Creator::class;
    protected $resource = CreatorResource::class;

    protected $columns = [
        'creator_id',
        'mobile', 
        'email',
        'email_verified_at', 
        'creator_name',
        'logo',
        'bio',
        'address',
        'IBAN_no',
        'Mawthooq_no',
        'birth_date',

        'deleted_at',
        'created_at',
        'updated_at',
    ];

    function index(Request $request) {
        return $this->indexInit($request, function ($items) {
            if (!auth('admin')->check()) {
                $items = $items->whereNull('deleted_at');
                return [$items->whereNotNull('logo')];
            }
            return [$items];
        });
    }

    function show($id) {
        return $this->showInit($id);
    }


    public function create()
    {
        try {
            $city_names = City::orderBy('city_name', 'ASC')->pluck('city_name')->toArray();
            $cat_names = Cat::orderBy('cat_name', 'ASC')->pluck('cat_name')->toArray();
            return $this->sendResponse(true, [
                'city_names' => $city_names,
                'cat_names' => $cat_names,
            ], '', null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [ 
                'creator_name' => 'required|min:3|max:60|unique:creators,creator_name',
                'mobile' => 'required|sa_mobile|unique:creators,mobile',
                'logo' => 'required|min:6|max:190',
                'bio' => 'required|min:10|max:200',
                'address' => 'required|min:10|max:190',
                'birth_date' => 'required|date|after:1925-07-11|before:' . Carbon::now()->subYears(10),
                'IBAN_no' => 'nullable|regex:/^SA\d{22}$/|size:24',
                'Mawthooq_no' => 'nullable|min:5|max:20',
                'cat_names' => 'required|array',
                'cat_names.*' => 'required|exists:cats,cat_name'
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;


            DB::beginTransaction();
            $creator = Creator::create([
                'mobile' => $request->mobile,
                'creator_name' => $request->creator_name,
                'logo' => $request->logo,
                'bio' => $request->bio,
                'address' => $request->address,
                'birth_date' => $request->birth_date,
                'IBAN_no' => $request->IBAN_no,
                'Mawthooq_no' => $request->Mawthooq_no,
            ]); 
            
            $cat_ids = Cat::withTrashed()->whereIn('cat_name', $request->cat_names)->pluck('cat_id')->toArray();

            $creator->cats()->sync($cat_ids);
            DB::commit();

            return $this->sendResponse(true, [
                'item' => new  CreatorResource($creator),
            ], trans('successfullUpdate'), null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

    function edit($id) {
        return $this->editInit($id);
    }
 
    public function update(Request $request, $id)
    { 
        try {
            if (auth()->user()->creator_id != $id && !auth('admin')->check()) {
                return $this->sendResponse(false, [], "You are not admin user", null, 400);
            }
            $requiredMobiel = auth('admin')->check()? 'required' : 'nullable';

            $validator = Validator::make([...$request->all(), 'id' => $id], [
                'id' => 'required|exists:creators,creator_id',
                'mobile' => $requiredMobiel . '|sa_mobile|unique:creators,mobile,' . $id . ',creator_id',
                'creator_name' => 'required|min:3|max:60|unique:creators,creator_name,' . $id . ',creator_id',
                'logo' => 'required|min:6|max:190',
                'bio' => 'required|min:10|max:200',
                'address' => 'required|min:10|max:190',
                'birth_date' => 'required|date|after:1925-07-11|before:' . Carbon::now()->subYears(10),
                'IBAN_no' => 'nullable|regex:/^SA\d{22}$/|size:24',
                'Mawthooq_no' => 'nullable|min:5|max:20',
                'cat_names' => 'required|array',
                'cat_names.*' => 'required|exists:cats,cat_name', 
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;
            $creator = Creator::withTrashed()->where('creator_id', $id)->first();


            DB::beginTransaction();
            $mobile = auth('admin')->check()? $request->mobile : $creator->mobile;

            $creator->update([
                'creator_name' => $request->creator_name,
                'mobile' => $mobile,
                'logo' => $request->logo,
                'bio' => $request->bio,
                'address' => $request->address,
                'birth_date' => $request->birth_date,
                'IBAN_no' => $request->IBAN_no,
                'Mawthooq_no' => $request->Mawthooq_no,
            ]);  

            $cat_ids = Cat::withTrashed()->whereIn('cat_name', $request->cat_names)->pluck('cat_id')->toArray();
            $creator->cats()->sync($cat_ids);
            DB::commit();

            return $this->sendResponse(true, [
                'item' => new  CreatorResource($creator),
            ], trans('successfullUpdate'), null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

    function destroy($id) {
        return $this->destroyInit($id);
    }

    function toggleActive($id, $state) {
        return $this->toggleActiveInit($id, $state);
    }
    
    function samplevideos(Request $request, $id) {
        try {
            if (auth()->user()->creator_id != $id && !auth('admin')->check()) {
                return $this->sendResponse(false, [], "You are not admin user", null, 400);
            }
            
            $validator = Validator::make([...$request->all(), 'id' => $id], [
                'id' => 'required|exists:creators,creator_id', 
                'sampleVideos' => 'nullable|array',
                'sampleVideos.*.video_url' => 'required|url|min:3|max:190',
                'sampleVideos.*.video_order_no' => 'required|numeric|min:0|max:10000',
                'sampleVideos.*.video_image_path' => 'required|min:10|max:190',
                'sampleVideos.*.video_description' => 'nullable|min:10|max:200',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;
            $creator = Creator::withTrashed()->where('creator_id', $id)->first();


            DB::beginTransaction();
            $creator->sampleVideos()->delete();
            $creator->sampleVideos()->createMany($request->sampleVideos);
 
            DB::commit();

            return $this->sendResponse(true, [
                'item' => new  CreatorResource($creator),
            ], trans('successfullUpdate'), null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    } 
}
