<?php

use App\Models\AccessToken;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Response;

use Illuminate\Support\HtmlString;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Kutia\Larafirebase\Facades\Larafirebase;

define('PAGINATION_COUNT', 10);

function zid_header(string $lang = 'ar'): array{
    $user = User::where('role_type', 'Admin')->first();
    return [
        'Accept' => 'application/json',
        'Accept-Language' => $lang,
        'Access-Token' => $user->managerToken,
        'Authorization' => 'Bearer ' . $user->authToken,
        'Store-Id' =>  app('currentTenant')->merchant_id,
    ];
}

function msgNumberHandler($msg){ 
    if($msg == null || $msg == 1) return ''; 
    return $msg;
}


function generateRandomPassword($length = 8) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
    $password = substr(str_shuffle($chars), 0, $length);
    return $password;
}

function executionTime($start_time, $end_time){
    $total_time = $end_time - $start_time;
    $minutes = floor($total_time / 60);
    $seconds = $total_time % 60;
    return sprintf("%02d:%02d", $minutes, $seconds);
}

function logToFile($fileName, $content) {
    $filePath = storage_path('app/'.$fileName);

    // Ensure that the file exists
    if (!file_exists($filePath)) {
        touch($filePath); // Create the file if it doesn't exist
    }

    // Append content to the file in a new line
    file_put_contents($filePath, $content . PHP_EOL, FILE_APPEND);
}



function generateRandomStringId($length = 8) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $id = substr(str_shuffle($chars), 0, $length);
    return $id . '-' . substr(str_shuffle($chars), 0, $length) . '-' . substr(str_shuffle($chars), 0, $length) . '-' . substr(str_shuffle($chars), 0, $length);
}

function apiResponse($status, $msg, $data="", $arrData=[]){
    $response = [
        'status'=> $status,
        'msg'=> $msg,
        'message'=> $msg,
    ];

    foreach ($arrData as $key => $value) {
        $response[$key] = $value;
    }

    $response ['data'] = $data ;

    return response()->json($response);
}

function checkValidator($validator)
{
    if($validator->fails()){
        return apiResponse(0, 'Not Valid data', [], ['errors'=>$validator->errors()]);
    }else return false;
}

function delete_img($img_path){
    if (file_exists($img_path)) {
        $path_info = pathinfo($img_path);
        $mask = $path_info['dirname'] . '/' . $path_info['filename'] . '*.*';
        array_map( "unlink", glob( $mask ) );
    }
}


function image_name($image, $image_name='', $size='')
{
    $size = ($size) ? '-'.$size : '';
    $image_name = ($image_name) ? $image_name : hexdec(uniqid());
    return $image_name . $size . '.' . $image->getClientOriginalExtension();
}

function getSrc($edit, $name)
{
    $img = $name;
    $imgSrc= $name.'Src';
    if($edit){
        if(file_exists($edit->$imgSrc())){
            return asset($edit->$imgSrc());
        }
    }
    return '';
}


function get_remote_file_info($url) {
    $localSrc = str_replace(url('/').'/', '', $url);
    if(file_exists($localSrc)){
        $imgsize = filesize($localSrc);
        return formatSizeUnits($imgsize);
    }
}

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824)
    {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    }
    elseif ($bytes >= 1048576)
    {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    }
    elseif ($bytes >= 1024)
    {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    }
    elseif ($bytes > 1)
    {
        $bytes = $bytes . ' bytes';
    }
    elseif ($bytes == 1)
    {
        $bytes = $bytes . ' byte';
    }
    else
    {
        $bytes = '0 bytes';
    }

    return $bytes;
}


function string_as_column($arr, $key, $string)
{
    foreach ($arr as $k => $v) {
        $arr[$k][$key] = $string;
    }
    return $arr;
}

function getAttr($obj, $child, $attr)
{
    return ($obj->$child) ? $obj->$child->$attr : '';
}

function sendNotification($data, $user_ids)
{
    // try{
        $device_tokens = AccessToken::whereIn('tokenable_id', $user_ids)->where('tokenable_id', '<>', Auth::id())->groupby('device_token')->pluck('device_token')->toArray();

        Larafirebase::withTitle($data['title'])
            ->withBody($data['body'])
            ->withImage($data['image'])
            ->withIcon('ic_stat_icon_marvel')
            ->withSound('default')
            // ->withClickAction('https://www.google.com')
            ->withPriority('high')
            ->withAdditionalData($data)
            ->sendNotification($device_tokens);

            return $device_tokens;

    // }catch(\Exception $e){
    //     report($e);
    //     return apiResponse(0, 'NO Notification Sent!!');
    // }
}
