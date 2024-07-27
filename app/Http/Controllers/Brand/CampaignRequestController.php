<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\CampaignRequestResource; 
use App\Http\Traits\DistroyTrait;
use App\Http\Traits\EditTrait;
use App\Http\Traits\IndexTrait;
use App\Http\Traits\ShowTrait;
use App\Http\Traits\ToggleActiveTrait;
use App\Models\AppConstants;
use App\Models\Campaign;
use App\Models\CampaignRequest; 
use App\Models\Statusable; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CampaignRequestController extends BaseApiController
{
    use IndexTrait, ShowTrait, EditTrait, DistroyTrait, ToggleActiveTrait;

    protected $table = 'campaign_requests';
    protected $model = CampaignRequest::class;
    protected $resource = CampaignRequestResource::class;

    protected $columns = [
        'request_id',
        'request_no',
        'campaign_id',
        'brand_id',
        'creator_id',
        'explanation',
        'request_status',
        'request_reject_reason',

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
                'campaign_id' => 'required|exists:campaigns,campaign_id',  
                'explanation' => 'required|min:5|max:500', 
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $campaign = Campaign::where('campaign_id', $request->campaign_id)
                ->where('campaign_status', 'Active')->first(); 
                  
            if(!$campaign){
                return $this->sendResponse(false, null, 'This is not active campaign!', null, 401);
            }
            
            if(!Auth()->user()->isCompleteProfile()){
                return $this->sendResponse(false, null, 'Please complete your profile firstly!', null, 401);
            }

            $campaignRequest = CampaignRequest::where('campaign_id', $request->campaign_id)
                ->where('creator_id', Auth()->user()->creator_id)->first(); 
            if($campaignRequest){
                return $this->sendResponse(false, null, 'You already applied request for this campaign!', null, 401);
            }

            $code = Str::random(8); 
            while ($this->model::where('request_no', $code)->exists()) {
                $code = Str::random(8);
            } 
 
            DB::beginTransaction();
            $item = $this->model::create([
                'request_no' => $code,
                'campaign_id' => $request->campaign_id,
                'brand_id' => $campaign->brand_id,
                'creator_id' => Auth()->user()->creator_id,
                'explanation' => $request->explanation,
                'request_status' => AppConstants::$request_states[0]
            ]); 

            Statusable::create([
                'statusable_id' => $item->request_id,
                'statusable_type' => 'CampaignRequest',
                'status' => AppConstants::$request_states[0],
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
                'explanation' => 'required|min:5|max:500', 
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
            
            if(!in_array($item->request_status, ['RequestRecieved', 'RequestRejected'])){
                return $this->sendResponse(false, null, 'The request status must be RequestRecieved or RequestRejected to update it!', null, 401);
            }
            
            if($item->explanation == $request->explanation){
                return $this->sendResponse(false, null, trans('YouMustUpdateTheExplanation'), ['explanation'=>[trans('YouMustUpdateTheExplanation')]], 401);
            }

            DB::beginTransaction();
            $item->update([ 
                'explanation' => $request->explanation,
                'request_status' => AppConstants::$request_states[0]
            ]);

            Statusable::create([
                'statusable_id' => $item->request_id,
                'statusable_type' => 'CampaignRequest',
                'status' => AppConstants::$request_states[0],
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
            $statesArr = auth('brand')->check()? ['RequestAccepted', 'RequestRejected'] : ['RequestRecieved', 'RequestAccepted', 'RequestRejected'];
            $requiredRejectReason = $request->request_status == 'RequestRejected'? 'required' : 'nullable';

            $validator = Validator::make([$this->columns[0] => $id, ...$request->all()], [
                $this->columns[0] => 'required|exists:' . $this->table . ',' . $this->columns[0],
                'request_status' => 'required|in:' .implode(',', $statesArr),
                'request_reject_reason' => $requiredRejectReason . '|min:5|max:500',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $item = $this->model::withTrashed()->where($this->columns[0], $id)->first();

            if (auth('brand')->check() && (!in_array($item->request_status, ['RequestRecieved', 'RequestRejected']) || $item->brand_id != Auth::user()->brand_id)){ 
                return $this->sendResponse(false, null, trans('youAreNotAllowedToDoThisAction'), null, 401);
            }

            $item->update(['request_status' => $request->request_status]);
            if($request->request_status){
                $item->update(['request_reject_reason' => $request->request_reject_reason]); 
            }
            
            Statusable::create([
                'statusable_id' => $item->request_id,
                'statusable_type' => 'CampaignRequest',
                'status' => $request->request_status,
                'reason' => $request->request_reject_reason,
            ]);
            
            return $this->sendResponse(true, [
                'item' => new $this->resource($item),
            ], trans('successfullUpdate'), null);
        // } catch (\Throwable $th) { 
        //     return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        // }
    }
}
