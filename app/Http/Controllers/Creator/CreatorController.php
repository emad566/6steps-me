<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\CreatorResource;
use App\Models\Creator;
use Illuminate\Support\Facades\Validator;


class CreatorController extends BaseApiController
{

    function show($id)
    {
        try {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|exists:creators,creator_id',
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $item = Creator::findOrFail($id);
            return $this->sendResponse(true, [
                'item' => new  CreatorResource($item),
            ], '', null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }
}
