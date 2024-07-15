<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\AppConstants;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class CityController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                ...AppConstants::$listVaidations,
                'sortColumn' => 'nullable|in:city_name,city_id'
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $items = City::withTrashed()->orderBy($request->sortColumn ?? 'city_id', $request->sortDirection ?? 'DESC');
            if (!auth('admin')->check()) {
                $items = $items->whereNull('deleted_at');
            }

            if ($request->city_name) {
                $items = $items->search('city_name', $request->city_name);
            }

            if ($request->dateFrom) {
                $items =  $items->where('created_at', '>=', Carbon::parse($request->dateFrom));
            }

            if ($request->dateTo) {
                $items =  $items->where('created_at', '<=', Carbon::parse($request->dateTo));
            }

            $items = $items->paginate($request->paginationCounter ?? AppConstants::$PerPage);
            return $this->sendResponse(true, data: ['items' => CityResource::collection($items)->response()->getData(true)], message: trans('Listed'));
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

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
     * Display the specified resource.
     */
    public function show($id)
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $validator = Validator::make(['city_id' => $id], [
                'city_id' => 'required|exists:cities,city_id'
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;
            $item = City::withTrashed()->where('city_id', $id)->first();

            $oldItem = $item;
            $item->forceDelete();

            return $this->sendResponse(true, [
                'item' => new CityResource($oldItem),
            ], trans('successfullDelete'), null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

    /**
     * toggle active.
     */
    public function toggleActive($id)
    {
        // try {
        $validator = Validator::make(['city_id' => $id], [
            'city_id' => 'required|exists:cities,city_id'
        ]);

        $check = $this->checkValidator($validator);
        if ($check) return $check;

        $item = City::withTrashed()->where('city_id', $id)->first();
        $item->update(['deleted_at' => $item->deleted_at ? null : Carbon::now()]);

        return $this->sendResponse(true, [
            'item' => new CityResource($item),
        ], trans('successfullUpdate'), null);
        // } catch (\Throwable $th) {
        //     return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        // }
    }
}
