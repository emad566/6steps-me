<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class FileController extends BaseApiController
{
    /**
     * upload File.
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpeg,bmp,png,pdf,xlsx,docx,mp4,avi,flv|max:10240',
            'path' => 'required|min:3|max:190',
            'old_file' => 'nullable|min:3|max:190',
        ]);


        $check = $this->checkValidator($validator);
        if ($check) return $check;

        if ($request->has('old_file')) {
            File::delete('storage/'. $request->old_file);
        }

        $data = uploadToStorage($request->file, $request->path);

        return $this->sendResponse(true, $data, trans('successfullUpload'), null);

    }

    /**
     * delete Upload File.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
                'files' => 'required|array',
        ]);
        $check = $this->checkValidator($validator);
        if ($check) return $check;

        $inputs = $request->all();

        foreach ($inputs['files'] as $file) {
            File::delete('storage/' . $file);
        }

        return $this->sendResponse(true, null, trans('successfullDelete'), null);
    }
}
