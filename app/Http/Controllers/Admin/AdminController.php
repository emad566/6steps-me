<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\AdminResource;
use App\Http\Traits\DistroyTrait;
use App\Http\Traits\EditTrait;
use App\Http\Traits\IndexTrait;
use App\Http\Traits\ShowTrait;
use App\Http\Traits\ToggleActiveTrait;
use App\Models\Admin;
use App\Models\Cat;
use App\Models\City;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends BaseApiController
{
    use IndexTrait, ShowTrait, EditTrait, DistroyTrait, ToggleActiveTrait;

    protected $table = 'admins';
    protected $model = Admin::class;
    protected $resource = AdminResource::class;

    protected $columns = [
        'admin_id',
        'admin_name',
        'email',
        'mobile',
        'logo', 
        'address',
        'websit_url',
        'email_verified_at',
        'password',

        'deleted_at',
        'created_at',
        'updated_at',
    ];

    function index(Request $request) {
        return $this->indexInit($request);
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
        try {
            $validator = Validator::make($request->all(), [ 
                'admin_name' => 'required|min:5|max:50|unique:admins,admin_name',
                'email' => 'required|min:5|max:60|unique:admins,admin_name',
                'mobile' => 'required|sa_mobile|unique:admins,admin_name',
                'logo' => 'nullable|min:5|max:190', 
                'address' => 'nullable|min:5|max:190',
                'websit_url' => 'nullable|url|min:5|max:190', 
                'password' => 'required|min:8|max:12',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;
 

            DB::beginTransaction();
            $item = Admin::create([
                'admin_name' => $request->admin_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'logo' => $request->logo,
                'address' => $request->address,
                'websit_url' => $request->websit_url,
                'password' => $request->password,
            ]); 
            DB::commit();

            return $this->sendResponse(true, [
                'item' => new  AdminResource($item),
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
            $validator = Validator::make([...$request->all(), 'id' => $id], [
                'id' => 'required|exists:admins,admin_id',
                'admin_name' => 'required|min:5|max:60|unique:admins,admin_name,' . $id . ',admin_id',
                'email' => 'required|min:5|max:60|unique:creators,email,' . $id . ',creator_id',
                'logo' => 'required|min:5|max:190',
                'mobile' => 'required|sa_mobile|unique:admins,admin_name',
                'logo' => 'nullable|min:5|max:190', 
                'address' => 'nullable|min:5|max:190',
                'websit_url' => 'nullable|url|min:5|max:190', 
                'password' => 'required|min:8|max:12',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;
 

            DB::beginTransaction();
            $item = Admin::create([
                'admin_name' => $request->admin_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'logo' => $request->logo,
                'address' => $request->address,
                'websit_url' => $request->websit_url,
                'password' => $request->password,
            ]); 
            DB::commit();

            return $this->sendResponse(true, [
                'item' => new  AdminResource($item),
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
     
}
