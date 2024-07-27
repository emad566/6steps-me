<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\BrandResource;
use App\Http\Traits\DistroyTrait;
use App\Http\Traits\EditTrait;
use App\Http\Traits\IndexTrait;
use App\Http\Traits\ShowTrait;
use App\Http\Traits\ToggleActiveTrait;
use App\Models\Brand;
use App\Models\Cat;
use App\Models\City;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandController extends BaseApiController
{
    use IndexTrait, ShowTrait, EditTrait, DistroyTrait, ToggleActiveTrait;

    protected $table = 'brands';
    protected $model = Brand::class;
    protected $resource = BrandResource::class;

    protected $columns = [
        'brand_id',
        'brand_name',
        'mobile',
        'email',
        'email_verified_at',
        'logo',
        'website_url',
        'description',
        'address',
        'branches_no',
        'tax_no',
        'cr_no',

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
                'brand_name' => 'required|min:3|max:60|unique:brands,brand_name',
                'email' => 'required|email|min:7|max:50|unique:brands,email',
                'logo' => 'required|min:5|max:190',
                'website_url' => 'nullable|url|min:5|max:190',
                'description' => 'required|min:5|max:200',
                'address' => 'required|min:5|max:190',
                'branches_no' => 'required|numeric|min:0|max:2000',
                'tax_no' => 'nullable|min:15|max:15',
                'cr_no' => 'nullable|min:10|max:10',
                'cat_names' => 'required|array',
                'cat_names.*' => 'required|exists:cats,cat_name',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;
 

            DB::beginTransaction();
            $item = Brand::create([
                'email' => $request->email,
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
            if (auth()->user()->brand_id != $id && !auth('admin')->check()) {
                return $this->sendResponse(false, [], "You are not admin user", null, 400);
            }
            $requiredEmail = auth('admin')->check()? 'required' : 'nullable'; 

            $validator = Validator::make([...$request->all(), 'id' => $id], [
                'id' => 'required|exists:brands,brand_id',
                'brand_name' => 'required|min:3|max:60|unique:brands,brand_name,' . $id . ',brand_id',
                'email' => $requiredEmail . '|email|unique:brands,email,' . $id . ',brand_id',
                'logo' => 'required|min:5|max:190',
                'mobile' => 'nullable|sa_mobile',
                'website_url' => 'nullable|url|min:5|max:190',
                'description' => 'required|min:5|max:200',
                'address' => 'required|min:5|max:190',
                'branches_no' => 'required|numeric|min:0|max:2000',
                'tax_no' => 'nullable|min:15|max:15',
                'cr_no' => 'nullable|min:10|max:10',
                'cat_names' => 'required|array',
                'cat_names.*' => 'required|exists:cats,cat_name',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $item = Brand::findOrFail($id);
            $email = auth('admin')->check()? $request->email : $item->email;

            DB::beginTransaction();
            $item->update([ 
                'brand_name' => $request->brand_name,
                'email' =>$email,
                'mobile' =>$request->mobile,
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
