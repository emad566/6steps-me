<?php


namespace App\Http\Traits;
 
use Illuminate\Support\Facades\Validator; 

trait DistroyTrait
{  
    public function destroyInit($id, $callBack= null)
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

            $oldItem = $item;
            $item->forceDelete();

            return $this->sendResponse(true, [
                'item' => new $this->resource($oldItem),
            ], trans('successfullDelete'), null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    } 
}
