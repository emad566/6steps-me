<?php


namespace App\Http\Traits;
 
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator; 

trait ShowTrait
{
    public function showInit($id, $callBack=null)
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

            return $this->sendResponse(true, [
                'item' => new $this->resource($item),
            ], trans('show'));
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

    public function edit($id)
    {
        try {
            $validator = Validator::make([$this->columns[0] => $id], [
                $this->columns[0] => 'required|exists:' . $this->table . ',' . $this->columns[0],
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;
            $item = $this->model::withTrashed()->where($this->columns[0], $id)->first();

            $response = $this->create();
            return $this->sendResponse(true, [
                'create' => $response?->getData()?->data,
                'item' => new $this->resource($item),
            ], trans('show'));
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

    public function destroy($id)
    {
        try {
            $validator = Validator::make([$this->columns[0] => $id], [
                $this->columns[0] => 'required|exists:' . $this->table . ',' . $this->columns[0],
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;
            $item = $this->model::withTrashed()->where($this->columns[0], $id)->first();

            $oldItem = $item;
            $item->forceDelete();

            return $this->sendResponse(true, [
                'item' => new $this->resource($oldItem),
            ], trans('successfullDelete'), null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

    /**
     * toggle active.
     */
    public function toggleActive($id, $state)
    {
        try {
            $validator = Validator::make([$this->columns[0] => $id], [
                $this->columns[0] => 'required|exists:' . $this->table . ',' . $this->columns[0],
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $item = $this->model::withTrashed()->where($this->columns[0], $id)->first();
            $item->update(['deleted_at' => $state == 1 ? null : Carbon::now()]);

            return $this->sendResponse(true, [
                'item' => new $this->resource($item),
            ], trans('successfullUpdate'), null);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }
}
