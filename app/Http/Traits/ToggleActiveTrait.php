<?php


namespace App\Http\Traits;

use App\Models\AppConstants;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

trait ToggleActiveTrait
{

    
    public function toggleActiveInit($id, $state, $callBack=null)
    {
        try {
            $validator = Validator::make([$this->columns[0] => $id], [
                $this->columns[0] => 'required|exists:' . $this->table . ',' . $this->columns[0],
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $item = $this->model::withTrashed()->where($this->columns[0], $id)->first();
            
            if($callBack){
                $response = $callBack($item);
                if($response[0] === false) return $response[1];
                $item = $response[0];
            }
            
            $item->update(['deleted_at' => $state == 1 ? null : Carbon::now()]);

            return $this->sendResponse(true, [
                'item' => new $this->resource($item),
            ], trans('successfullUpdate'), null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }
}
