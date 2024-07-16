<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Traits\ControllerTrait;
use App\Models\AppConstants;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class CityController extends BaseApiController
{

    use ControllerTrait;

    protected $table = 'cities';
    protected $model = City::class;
    protected $resource = CityResource::class;

    protected $columns = [
        'city_id',
        'city_name',

        'deleted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {

            return $this->sendResponse(true, [], '', null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'city_name' => 'required|min:3|max:50|unique:cities,city_name'
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $item = City::create([
                'city_name' => $request->city_name,
                'country_name' => 'Saudi Arabia',
            ]);

            return $this->sendResponse(true, [
                'item' => new CityResource($item),
            ], trans('CityegoryHasBeenCreated'));
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $validator = Validator::make(['city_id' => $id], [
                'city_id' => 'required|exists:cities,city_id'
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;
            $item = City::withTrashed()->where('city_id', $id)->first();


            return $this->sendResponse(true, [
                'item' => new CityResource($item),
            ], trans('show'));
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make([...$request->all(), 'city_id' => $id], [
                'city_id' => 'required|exists:cities,city_id',
                'city_name' => 'required|unique:cities,city_name,' . $id . ',city_id',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;
            $item = City::where('city_id', $id)->first();


            $item->update(['city_name' => $request->city_name]);

            return $this->sendResponse(true, [
                'item' => new CityResource($item),
            ], trans('successfullUpdate'), null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

}
