<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\API\BaseApiController; 
use App\Http\Resources\CampaignResource;
use App\Http\Traits\DistroyTrait;
use App\Http\Traits\EditTrait;
use App\Http\Traits\IndexTrait;
use App\Http\Traits\ShowTrait;
use App\Http\Traits\ToggleActiveTrait;
use App\Models\AppConstants;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Cat;
use App\Models\City;
use App\Models\Statusable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CampaignController extends BaseApiController
{
    use IndexTrait, ShowTrait, EditTrait, DistroyTrait, ToggleActiveTrait;

    protected $table = 'campaigns';
    protected $model = Campaign::class;
    protected $resource = CampaignResource::class;

    protected $columns = [
        'campaign_id', 
        'brand_id',
        'campaign_no',
        'campaign_title',
        'campaign_description',
        'start_at',
        'close_at',
        'conditions',
        'product_image',
        'ugc_no',
        'ugc_videos_no',
        'video_seconds_min',
        'video_seconds_max',
        'video_price',
        'total_price',
        'is_usg_show',
        'is_brand_show',
        'is_tiktok',
        'is_instagram',
        'is_youtube',
        'is_sent_to_content_creator',
        'campaign_status',
        'reject_reason',

        'deleted_at',
        'created_at',
        'updated_at',
    ];

    function index(Request $request) { 
        return $this->indexInit($request, function ($items) {
            if (!auth('admin')->check()) {
                if(auth('creator')->check()){
                    $items = $items->where('campaign_status', 'Active');
                }
                $items = $items->where(Authed()->primaryKey, Authed()->id);
                $items = $items->whereNull('deleted_at');
            }
            return [$items];
        });
    }

    function show($id) {
        return $this->showInit($id, function ($item){
            if (!auth('admin')->check()) {
                $pKey = Authed()->primaryKey; 
                if($item->$pKey != Authed()->id || $item->deleted_at){
                    return [
                        false,
                        $this->sendResponse(false, null, trans('youAreNotAllowedToDoThisAction'), null, 401)
                    ];
                } 
            }
            return [$item];
        });
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
        // try {
            $brand_id_state = 'nullable';
            
            if(auth('admin')->check()){
                $brand_id_state = 'required';
                $brand_id =$request->brand_id;
            }else if(auth('brand')->check()){
                $brand_id = Auth::user()->brand_id;
            }else{
                return $this->sendResponse(false, null, 'You are not allowed to create campaign', null, 401);
            }
 
            $validator = Validator::make($request->all(), [
                'brand_id' => $brand_id_state .'|exists:brands,brand_id',
                'campaign_title' => 'required|min:10|max:190|unique:campaigns,campaign_title',
                'campaign_description' => 'required|min:20|max:200',
                'start_at' => 'required|date_format:Y-m-d\TH:i:s.u\Z|after:' . Carbon::now()->toISOString(),
                'close_at' => 'required|date_format:Y-m-d\TH:i:s.u\Z|after:start_at',
                'conditions' => 'required|min:20|max:200',
                'product_image' => 'required|min:10|max:190',
                'ugc_no' => 'required|numeric|min:1|max:100',
                'ugc_videos_no' => 'required|numeric|min:1|max:1000',
                'video_seconds_min' => 'required|numeric|min:1|max:10800',
                'video_seconds_max' => 'required|numeric|min:1|max:10800',
                'video_price' => 'required|min:1|max:10000',
                'is_usg_show' => 'required|in:0,1',
                'is_brand_show' => 'required|in:0,1',
                'is_tiktok' => 'required|in:0,1',
                'is_instagram' => 'required|in:0,1',
                'is_youtube' => 'required|in:0,1',
                'is_sent_to_content_creator' => 'required|in:0,1',
                'cat_names' => 'required|array',
                'cat_names.*' => 'required|exists:cats,cat_name',
                'city_names' => 'required|array',
                'city_names.*' => 'required|exists:cities,city_name',

            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $brand = Brand::find($brand_id);

            if(!$brand->isCompleteProfile()){
                return $this->sendResponse(false, null, 'Please complete your profile firstly!', null, 401);
            }

            $campaignCode = Str::random(8); 
            
            while (Campaign::where('campaign_no', $campaignCode)->exists()) {
                $campaignCode = Str::random(8);
            } 
 
            DB::beginTransaction();
            $item = Campaign::create([
                'campaign_title' => $request->campaign_title,
                'campaign_no' =>  $campaignCode,
                'brand_id' => $brand_id,
                'campaign_description' => $request->campaign_description,
                'start_at' => $request->start_at,
                'close_at' => $request->close_at,
                'conditions' => $request->conditions,
                'product_image' => $request->product_image,
                'ugc_no' => $request->ugc_no,
                'ugc_videos_no' => $request->ugc_videos_no,
                'video_seconds_min' => $request->video_seconds_min,
                'video_seconds_max' => $request->video_seconds_max,
                'video_price' => $request->video_price,
                'is_usg_show' => $request->is_usg_show,
                'is_brand_show' => $request->is_brand_show,
                'is_tiktok' => $request->is_tiktok,
                'is_instagram' => $request->is_instagram,
                'is_youtube' => $request->is_youtube,
                'is_sent_to_content_creator' => $request->is_sent_to_content_creator,
                'cat_names' => 'required|array',
                'cat_names.*' => 'required|exists:cats,cat_name',
                'city_names' => 'required|array',
                'city_names.*' => 'required|exists:cities,city_name',
            ]);

            $cat_ids = Cat::withTrashed()->whereIn('cat_name', $request->cat_names)->pluck('cat_id')->toArray();
            $item->cats()->sync($cat_ids); 

            $city_ids = City::withTrashed()->whereIn('city_name', $request->city_names)->pluck('city_id')->toArray();
            $item->cities()->sync($city_ids); 
            Statusable::create([
                'statusable_id' => $item->campaign_id,
                'statusable_type' => 'Campaign',
                'status' => 'UnderReview',
            ]);

            DB::commit();

            return $this->sendResponse(true, [
                'item' => new CampaignResource($item),
            ], trans('CampaignHasBeenCreated'));
        // } catch (\Throwable $th) {
        //     DB::rollBack();
        //     return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        // }
    }

    function edit($id) {
        return $this->editInit($id, function ($item){
            if (!auth('admin')->check()) {
                $pKey = Authed()->primaryKey; 
                if($item->$pKey != Authed()->id || $item->deleted_at){
                    return [
                        false,
                        $this->sendResponse(false, null, trans('youAreNotAllowedToDoThisAction'), null, 401)
                    ];
                } 
            }
            return [$item];
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make([...$request->all(), 'campaign_id' => $id], [
                'campaign_id' => 'required|exists:campaigns,campaign_id',
                'campaign_title' => 'required|min:10|max:190|unique:campaigns,campaign_title,' . $id . ',campaign_id',
                'campaign_description' => 'required|min:20|max:200',
                'start_at' => 'required|date|date_format:Y-m-d H:i:s',
                'close_at' => 'required|date|date_format:Y-m-d H:i:s|after:' . Carbon::now(),
                'conditions' => 'required|min:20|max:200',
                'product_image' => 'required|min:10|max:190',
                'ugc_no' => 'required|numeric|min:1|max:100',
                'ugc_videos_no' => 'required|numeric|min:1|max:1000',
                'video_seconds_min' => 'required|numeric|min:1|max:10800',
                'video_seconds_max' => 'required|numeric|min:1|max:10800',
                'video_price' => 'required|min:1|max:10000',
                'is_usg_show' => 'required|in:0,1,true,false',
                'is_brand_show' => 'required|in:0,1,true,false',
                'is_tiktok' => 'required|in:0,1,true,false',
                'is_instagram' => 'required|in:0,1,true,false',
                'is_youtube' => 'required|in:0,1,true,false',
                'is_sent_to_content_creator' => 'required|in:0,1,true,false',
                'cat_names' => 'required|array',
                'cat_names.*' => 'required|exists:cats,cat_name',
                'city_names' => 'required|array',
                'city_names.*' => 'required|exists:cities,city_name',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;
            $item = Campaign::withTrashed()->where('campaign_id', $id)->first();

            if(!auth('admin')->check() && $item->brand_id != Auth::user()->brand_id){
                return $this->sendResponse(false, null, trans('youAreNotAllowedToDoThisAction'), null, 401);
            }

            DB::beginTransaction();
            $item->update([
                'campaign_status' => AppConstants::$campain_states[0],
                'campaign_title' => $request->campaign_title,
                'campaign_description' => $request->campaign_description,
                'start_at' => $request->start_at,
                'close_at' => $request->close_at,
                'conditions' => $request->conditions,
                'product_image' => $request->product_image,
                'ugc_no' => $request->ugc_no,
                'ugc_videos_no' => $request->ugc_videos_no,
                'video_seconds_min' => $request->video_seconds_min,
                'video_seconds_max' => $request->video_seconds_max,
                'video_price' => $request->video_price,
                'is_usg_show' => $request->is_usg_show,
                'is_brand_show' => $request->is_brand_show,
                'is_tiktok' => $request->is_tiktok,
                'is_instagram' => $request->is_instagram,
                'is_youtube' => $request->is_youtube,
                'is_sent_to_content_creator' => $request->is_sent_to_content_creator,
            ]);


            $cat_ids = Cat::withTrashed()->whereIn('cat_name', $request->cat_names)->pluck('cat_id')->toArray();
            $item->cats()->sync($cat_ids);

            $city_ids = City::withTrashed()->whereIn('city_name', $request->city_names)->pluck('city_id')->toArray();
            $item->cities()->sync($city_ids);

            Statusable::create([
                'statusable_id' => $item->campaign_id,
                'statusable_type' => 'Campaign',
                'status' => 'UnderReview',
            ]);
            DB::commit();

            return $this->sendResponse(true, [
                'item' => new CampaignResource($item),
            ], trans('successfullUpdate'), null);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

    function destroy($id) {
        return $this->destroyInit($id);
    }

    function toggleActive($id, $state) {
        return $this->toggleActiveInit($id, $state);
    }

    function updateStatus(Request $request, $id) {
        try{ 
            $campain_statesArr = auth('brand')->check()? ['Active', 'Ended', 'Stoped'] : AppConstants::$campain_states;
            $requiredRejectReason = $request->campaign_status == 'Rejected'? 'required' : 'nullable';

            $validator = Validator::make([$this->columns[0] => $id, ...$request->all()], [
                $this->columns[0] => 'required|exists:' . $this->table . ',' . $this->columns[0],
                'campaign_status' => 'required|in:' .implode(',', $campain_statesArr),
                'reject_reason' => $requiredRejectReason . '|min:5|max:500',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $item = $this->model::withTrashed()->where($this->columns[0], $id)->first();

            if (auth('brand')->check() && !in_array($item->campaign_status, ['Ended', 'Stoped', 'Active'])){ 
                return $this->sendResponse(false, null, trans('youAreNotAllowedToDoThisAction'), null, 401);
            }

            $item->update(['campaign_status' => $request->campaign_status]);
            if($request->campaign_status){
                $item->update(['reject_reason' => $request->reject_reason]); 
            }
            
            Statusable::create([
                'statusable_id' => $item->campaign_id,
                'statusable_type' => 'Campaign',
                'status' => $request->campaign_status,
                'reason' => $request->reject_reason,
            ]);
            
            return $this->sendResponse(true, [
                'item' => new CampaignResource($item),
            ], trans('successfullUpdate'), null);
        } catch (\Throwable $th) { 
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }
}
