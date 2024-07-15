<?php


namespace App\Http\Controllers\API;

use App\Exports\CollectionExport;
use App\Http\Controllers\Controller;
use App\Models\Downloads;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;


class BaseApiController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */

    public function sendResponse($status = true, $data = null, $message = '', $errors = null, $code = 200)
    {
        $response = [
            'status' =>  $status,
            'message' => $message,
            'data'    => $data,
            'errors' => $status === true ? $errors : (count($errors ?? [], COUNT_RECURSIVE) > 1 ? $errors : ['message' => [$message]]),
            // 'response_code' => $code,
        ];
        return response()->json($response, $code);
    }

    public function sendServerError($msg = '', $data = null, $th = false)
    {
        $thStr = $th ? $th->getMessage() : '';
        return $this->sendResponse(false, $data, 'Server Technical Error: ' . $msg . " $thStr", null, 500);
    }

    public function checkValidator($validator, $data = null)
    {
        $errors = $validator->errors();
        $errorsArr = json_decode(json_encode($errors), true);

        if ($validator->fails()) {
            $message = '';
            $errorsArrValues = array_values($errorsArr);

            if (!empty($errorsArrValues) && isset($errorsArrValues[0][0])) {
                $message = $errorsArrValues[0][0];
                if (count($errorsArrValues ?? []) > 1) {
                    $message .= trans('and') . (count($errorsArrValues ?? []) - 1) . trans('moreValidation');
                }
            }

            $response = [
                'status' => false,
                'message' => $message,
                'data'    => $data,
                'errors' => $errors,
                // 'response_code' => 200,
            ];

            return response()->json($response, 400);
        } else return false;
    }

    public function permission_logout()
    {
        try {
            return $this->sendResponse(false, ['unAuth' => 1], "Unauthenticated.", [], 401);
        } catch (\Throwable $th) {
        }
    }

    public function export($collection, $filename, $type, $heading)
    {
        $uid = \Auth::id();
        $filename .= "-" . Carbon::now();
        $path = "$uid/$type/$type-$filename.xlsx";

        $full_path = storage_path("app/$path");
        $download = Downloads::create([
            'type' => $type,
            'path' => $path,
            'status' => 0,
        ]);

        Excel::store(new CollectionExport($collection, $heading), $path);

        $download->update([
            'status' => 1,
        ]);

        return response()->download($full_path)->deleteFileAfterSend(false);
    }
}
