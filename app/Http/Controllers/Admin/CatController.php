<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\CatResource;
use App\Http\Traits\ControllerTrait;
use App\Models\Cat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CatController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     */
    use ControllerTrait;

    protected $table = 'cats';
    protected $model = Cat::class;
    protected $resource = CatResource::class;

    protected $columns = [
        'cat_id',
        'cat_name',

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
                'cat_name' => 'required|min:3|max:50|unique:cats,cat_name'
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $item = Cat::create(['cat_name' => $request->cat_name]);

            return $this->sendResponse(true, [
                'item' => new CatResource($item),
            ], trans('CategoryHasBeenCreated'));
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
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
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
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }
}
