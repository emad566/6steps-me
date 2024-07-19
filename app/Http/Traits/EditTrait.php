<?php


namespace App\Http\Traits;

use Illuminate\Support\Facades\Validator;


trait EditTrait
{ 

    public function editInit($id, $callBack=null)
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

            $response = $this->create();
            return $this->sendResponse(true, [
                'create' => $response?->getData()?->data,
                'item' => new $this->resource($item),
            ], trans('show'));
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }
 
}
