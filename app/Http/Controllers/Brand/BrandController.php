<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\BrandResource;
use App\Models\Brand; 
use Illuminate\Support\Facades\Validator;


class BrandController extends BaseApiController
{

    function show($id)
    {
        try {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|exists:brands,brand_id',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $item = Brand::findOrFail($id);
            return $this->sendResponse(true, [
                'item' => new  BrandResource($item),
            ], '', null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }
}
