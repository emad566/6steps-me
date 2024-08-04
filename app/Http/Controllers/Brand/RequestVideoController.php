<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\CampaignRequestResource;
use App\Http\Resources\RequestVideoResource;
use App\Http\Traits\DistroyTrait;
use App\Http\Traits\EditTrait;
use App\Http\Traits\IndexTrait;
use App\Http\Traits\ShowTrait;
use App\Http\Traits\ToggleActiveTrait;
use App\Models\AppConstants;
use App\Models\Campaign;
use App\Models\CampaignRequest;
use App\Models\RequestVideo;
use App\Models\Statusable; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RequestVideoController extends BaseApiController
{
    use IndexTrait, ShowTrait, EditTrait, DistroyTrait, ToggleActiveTrait;

    protected $table = 'request_videos';
    protected $model = RequestVideo::class;
    protected $resource = RequestVideoResource::class;

    protected $columns = [
        'video_id',
        'video_no',
        'request_id',
        'campaign_id',
        'brand_id',
        'creator_id',
        'video_url',
        'video_image_path',
        'video_description',
        'video_status',
        'video_reject_reason',

        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function index(Request $request) { 
        return $this->indexInit($request, function ($items) use($request) {
            if (!auth('admin')->check()) {
                $items = $items->where(Authed()->primaryKey, Authed()->id);
                $items = $items->whereNull('deleted_at');
            }
            return [$items];
        });
    }

    public function show($id) {
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
            
            return $this->sendResponse(true, [ 
            ], '', null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

    public function store(Request $request)
    {
        // try { 
            $validator = Validator::make($request->all(), [ 
                'request_id' => 'required|exists:campaign_requests,request_id',  
                'video_url' => 'required|url|min:5|max:191',
                'video_image_path' => 'required|min:5|max:191',
                'video_description' => 'required|min:5|max:500',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $campaignRequest = CampaignRequest::where('request_id', $request->request_id)
                ->where('creator_id', Auth()->user()->creator_id)
                ->first();  

            if(!$campaignRequest){
                return $this->sendResponse(false, null, 'This is not Accepted Request!', null, 401);
            }

            if($campaignRequest->request_status != 'RequestAccepted'){
                return $this->sendResponse(false, null, 'This is not Accepted Request!', null, 401);
            }

            if($campaignRequest->campaign->campaign_status != 'Active'){
                return $this->sendResponse(false, null, 'This is not active campaign!', null, 401);
            }
            
            $video = RequestVideo::where('request_id', $request->request_id)
                ->where('video_url', $request->video_url)->first(); 

            if($video){
                return $this->sendResponse(false, null, 'You already applied this video url!', null, 401);
            }

            $code = Str::random(8); 
            while ($this->model::where('video_no', $code)->exists()) {
                $code = Str::random(8);
            } 
 
            DB::beginTransaction();
            $item = $this->model::create([
                'video_no' => $code,
                'request_id' => $request->request_id,
                'campaign_id' => $campaignRequest->campaign_id,
                'brand_id' => $campaignRequest->brand_id,
                'creator_id' => Auth()->user()->creator_id,
                'video_url' => $request->video_url,
                'video_image_path' => getRelative($request->video_image_path),
                'video_description' => $request->video_description,
                'video_status' => AppConstants::$video_states[0]
            ]); 

            Statusable::create([
                'statusable_id' => $item->request_id,
                'statusable_type' => 'RequestVideo',
                'status' => AppConstants::$video_states[0],
            ]);
            DB::commit();

            return $this->sendResponse(true, [
                'item' => new $this->resource($item),
            ], trans('successfullUpdate'));
        // } catch (\Throwable $th) {
        //     DB::rollBack();
        //     return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        // }
    }

    public function edit($id) {
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
            $validator = Validator::make([...$request->all(), $this->columns[0] => $id], [ 
                $this->columns[0] => 'required|exists:' . $this->table . ',' . $this->columns[0],  
                'video_url' => 'required|url|min:5|max:191',
                'video_image_path' => 'required|min:5|max:191',
                'video_description' => 'required|min:5|max:500',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $item = $this->model::where($this->columns[0], $id)->first();
            

            if(!auth('admin')->check() && $item->creator_id != Auth::user()->creator_id){
                return $this->sendResponse(false, null, trans('youAreNotAllowedToDoThisAction'), null, 401);
            }

            if($item->campaign->campaign_status != 'Active'){
                return $this->sendResponse(false, null, 'This is not active campaign!', null, 401);
            }
            
            if(!in_array($item->video_status, ['VideoRecieved', 'VideoRejected'])){
                return $this->sendResponse(false, null, 'The request status must be VideoRecieved or VideoRejected to update it!', null, 401);
            }
             

            DB::beginTransaction();
            $item->update([ 
                'video_url' => $request->video_url,
                ''video_image_path' => getRelative($request->video_image_path),
                'video_description' => $request->video_description,
                'video_status' => AppConstants::$video_states[0]
            ]); 

            Statusable::create([
                'statusable_id' => $item->request_id,
                'statusable_type' => 'RequestVideo',
                'status' => AppConstants::$video_states[0],
            ]);
            DB::commit();

            return $this->sendResponse(true, [
                'item' => new $this->resource($item),
            ], trans('successfullUpdate'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        } 
    }

    public function destroy($id) {
        return $this->destroyInit($id);
    }

    public function toggleActive($id, $state) {
        return $this->toggleActiveInit($id, $state, function ($item){
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

    public function updateStatus(Request $request, $id) {
        // try{ 
            $statesArr = ['VideoRecieved', 'VideoAccepted', 'VideoRejected'];
            $requiredRejectReason = $request->video_status == 'VideoRejected'? 'required' : 'nullable';

            $validator = Validator::make([$this->columns[0] => $id, ...$request->all()], [
                $this->columns[0] => 'required|exists:' . $this->table . ',' . $this->columns[0],
                'video_status' => 'required|in:' .implode(',', $statesArr),
                'video_reject_reason' => $requiredRejectReason . '|min:5|max:500',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $item = $this->model::withTrashed()->where($this->columns[0], $id)->first();

            if (auth('brand')->check() && (!in_array($item->video_status, ['VideoRecieved', 'VideoRejected']) || $item->brand_id != Auth::user()->brand_id)){ 
                return $this->sendResponse(false, null, trans('youAreNotAllowedToDoThisAction'), null, 401);
            }

            $item->update(['video_status' => $request->video_status]);
            if($request->video_status){
                $item->update(['video_reject_reason' => $request->video_reject_reason]); 
            }
            
            Statusable::create([
                'statusable_id' => $item->video_id,
                'statusable_type' => 'RequestVideo',
                'status' => $request->video_status,
                'reason' => $request->video_reject_reason,
            ]);
            
            return $this->sendResponse(true, [
                'item' => new $this->resource($item),
            ], trans('successfullUpdate'), null);
        // } catch (\Throwable $th) { 
        //     return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        // }
    }
}
