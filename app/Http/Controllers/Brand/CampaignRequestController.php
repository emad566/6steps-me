<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\CampaignRequestResource;
use App\Http\Resources\CampaignResource;
use App\Http\Traits\DistroyTrait;
use App\Http\Traits\EditTrait;
use App\Http\Traits\IndexTrait;
use App\Http\Traits\ShowTrait;
use App\Http\Traits\ToggleActiveTrait;
use App\Models\AppConstants;
use App\Models\Campaign;
use App\Models\CampaignRequest;
use App\Models\Cat;
use App\Models\City;
use App\Models\Statusable;
use Carbon\Carbon;
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
        'reject_reason',

        'deleted_at',
        'created_at',
        'updated_at',
    ];

    function index(Request $request) {
        return $this->indexInit($request, function ($items) {
            if (!auth('admin')->check()) {
                $items = $items->whereNull('deleted_at');
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
            
            return $this->sendResponse(true, [ 
            ], '', null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

    public function store(Request $request)
    { 

    }

    function edit($id) {
        return $this->editInit($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        
        
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
                'statusable_id' => $item->id,
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
