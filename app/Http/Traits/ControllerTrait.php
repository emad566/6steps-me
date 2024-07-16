<?php


namespace App\Http\Traits;

use App\Models\AppConstants;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

trait ControllerTrait
{

    function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                ...AppConstants::$listVaidations,
                'sortColumn' => 'nullable|in:' . implode(',', $this->columns),
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;

            $items = $this->model::withTrashed()->orderBy($request->sortColumn ?? $this->columns[0], $request->sortDirection ?? 'DESC');
            if (!auth('admin')->check()) {
                $items = $items->whereNull('deleted_at');
            }

            foreach ($this->columns as $colum) {
                if ($request->$colum) {
                    $items = $items->search($colum, $request->$colum);
                }
            }

            if ($request->dateFrom) {
                $items =  $items->where('created_at', '>=', Carbon::parse($request->dateFrom));
            }

            if ($request->dateTo) {
                $items =  $items->where('created_at', '<=', Carbon::parse($request->dateTo));
            }

            $items = $items->paginate($request->paginationCounter ?? AppConstants::$PerPage);
            return $this->sendResponse(true, data: ['items' => $this->resource::collection($items)->response()->getData(true)], message: trans('Listed'));
        } catch (\Throwable $th) {
            return $this->sendResponse(false, null, trans('technicalError'), null, 500);
        }
    }

    public function show($id)
    {
        try {
            $validator = Validator::make([$this->columns[0] => $id], [
                $this->columns[0] => 'required|exists:' . $this->table . ',' . $this->columns[0],
            ]);

            $check = $this->checkValidator($validator);
            if ($check) return $check;
            $item = $this->model::withTrashed()->where($this->columns[0], $id)->first();


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
