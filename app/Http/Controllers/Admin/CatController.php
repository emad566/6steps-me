<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\CatResource;
use App\Models\AppConstants;
use App\Models\Cat;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class CatController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // try {
            $validator = Validator::make($request->all(), [
                ...AppConstants::$listVaidations,
                'sortColumn' => 'nullable|in:cat_name,cat_id'
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $items = Cat::withTrashed()->orderBy($request->sortColumn ?? 'cat_id', $request->sortDirection ?? 'DESC');
            if (!auth('admin')->check()) {
                $items = $items->whereNull('deleted_at');
            }

            if($request->cat_name) {
                $items = $items->where('cat_name', $request->cat_name);
            }

            if ($request->dateFrom) {
                $items =  $items->where('created_at', '>=', Carbon::parse($request->dateFrom));
            }

            if ($request->dateTo) {
                $items =  $items->where('created_at', '<=', Carbon::parse($request->dateTo));
            }

            $items = $items->paginate($request->paginationCounter ?? AppConstants::$PerPage);
            return $this->sendResponse(true, data: ['items' => CatResource::collection($items)->response()->getData(true)], message: trans('Listed'));
        // } catch (\Throwable $th) {
        //     return $this->sendResponse(false, null, trans('technicalError'));
        // }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {


            return $this->sendResponse(true, [

            ], '', null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cat_name' => 'required|min:3|max:50|unique:cats,cat_name'
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $item = Cat::create(['cat_name' => $request->cat_name]);

            return $this->sendResponse(true, [
                'item' => new CatResource($item),
            ], trans('CategoryHasBeenCreated'));
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $validator = Validator::make(['cat_id' => $id], [
                'cat_id' => 'required|exists:cats,cat_id'
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;
            $item = Cat::withTrashed()->where('cat_id', $id)->first();


            return $this->sendResponse(true, [
                'item' => new CatResource($item),
            ], trans('show'));
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $validator = Validator::make(['cat_id' => $id], [
                'cat_id' => 'required|exists:cats,cat_id'
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;
            $item = Cat::withTrashed()->where('cat_id', $id)->first();


            return $this->sendResponse(true, [
                'item' => new CatResource($item),
            ], trans('show'));
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // try {
            $validator = Validator::make([...$request->all(), 'cat_id' => $id], [
                'cat_id' => 'required|exists:cats,cat_id',
                'cat_name' => 'required|unique:cats,cat_name,' . $id . ',cat_id',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;
            $item = Cat::where('cat_id', $id)->first();


            $item->update(['cat_name' => $request->cat_name]);

            return $this->sendResponse(true, [
                'item' => new CatResource($item),
            ], trans('successfullUpdate'), null);
        // } catch (\Throwable $th) {
        //     return $this->sendResponse(false, null, trans('technicalError'));
        // }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // try {
            $validator = Validator::make(['cat_id' => $id], [
                'cat_id' => 'required|exists:cats,cat_id'
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;
            $item = Cat::withTrashed()->where('cat_id', $id)->first();

            $oldItem = $item;
            $item->forceDelete();

            return $this->sendResponse(true, [
                'item' => new CatResource($oldItem),
            ], trans('successfullDelete'), null);
        // } catch (\Throwable $th) {
        //     return $this->sendResponse(false, null, trans('technicalError'));
        // }
    }

    /**
     * toggle active.
     */
    public function toggleActive($id)
    {
        // try {
            $validator = Validator::make(['cat_id' => $id], [
                'cat_id' => 'required|exists:cats,cat_id'
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $item = Cat::withTrashed()->where('cat_id', $id)->first();
            $item->update(['deleted_at' => $item->deleted_at? null : Carbon::now()]);

            return $this->sendResponse(true, [
                'item' => new CatResource($item),
            ], trans('successfullUpdate'), null);

        // } catch (\Throwable $th) {
        //     return $this->sendResponse(false, null, trans('technicalError'));
        // }
    }
}
