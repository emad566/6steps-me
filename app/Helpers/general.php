<?php

use App\Models\AccessToken;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Kutia\Larafirebase\Facades\Larafirebase;
use Illuminate\Support\Facades\Storage;

function Authed() : AuthService {
    return new AuthService();
} 

function generateSaudiMobileNumber($faker) {
    // Start with the Saudi country code
    $countryCode = '966';

    // Generate a random mobile number starting with 5
    $mobileNumber = '5' . $faker->numerify('########'); // Generates a 9 digit number

    return $countryCode . $mobileNumber; // Return the full number
}

function generateRandomPassword($length = 8)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
    $password = substr(str_shuffle($chars), 0, $length);
    return $password;
}

function executionTime($start_time, $end_time)
{
    $total_time = $end_time - $start_time;
    $minutes = floor($total_time / 60);
    $seconds = $total_time % 60;
    return sprintf("%02d:%02d", $minutes, $seconds);
}

function logToFile($fileName, $content)
{
    $filePath = storage_path('app/' . $fileName);

    // Ensure that the file exists
    if (!file_exists($filePath)) {
        touch($filePath); // Create the file if it doesn't exist
    }

    // Append content to the file in a new line
    file_put_contents($filePath, $content . PHP_EOL, FILE_APPEND);
}



function generateRandomStringId($length = 8)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $id = substr(str_shuffle($chars), 0, $length);
    return $id . '-' . substr(str_shuffle($chars), 0, $length) . '-' . substr(str_shuffle($chars), 0, $length) . '-' . substr(str_shuffle($chars), 0, $length);
}



function delete_img($img_path)
{
    if (file_exists($img_path)) {
        $path_info = pathinfo($img_path);
        $mask = $path_info['dirname'] . '/' . $path_info['filename'] . '*.*';
        array_map("unlink", glob($mask));
    }
}



function getSrc($edit, $name)
{
    $img = $name;
    $imgSrc = $name . 'Src';
    if ($edit) {
        if (file_exists($edit->$imgSrc())) {
            return asset($edit->$imgSrc());
        }
    }
    return '';
}


function get_remote_file_info($url)
{
    $localSrc = str_replace(url('/') . '/', '', $url);
    if (file_exists($localSrc)) {
        $imgsize = filesize($localSrc);
        return formatSizeUnits($imgsize);
    }
}

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
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
    try {
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
    } catch (\Exception $e) {
        report($e);
        return apiResponse(0, 'NO Notification Sent!!');
    }
}


/**
 * Base Url For Image From Dashboard Project
 */
if (!function_exists('FileBaseURl')) {
    function FileBaseURl($file)
    {
        if ($file) {
            return env('APP_FILE_URL') . $file;
        }
    }
}


if (!function_exists('convertToMinutes')) {
    function convertToMinutes($second)
    {
        if ($second == 0) {
            return 0;
        }
        $second = $second / 60;
        return round($second, 2, PHP_ROUND_HALF_UP);
    }
}

if (!function_exists('convertToKM')) {
    function convertToKm($meter)
    {
        if ($meter == 0) {
            return 0;
        }
        $meter = $meter / 1000;
        return round($meter, 2, PHP_ROUND_HALF_UP);
    }
}




/**
 * Get list of languages
 */

if (!function_exists('languages')) {
    function languages()
    {
        $languages = Language::all();
        return $languages;
    }
}
/**
 * upload64
 */
if (!function_exists('upload64')) {
    function upload64($file, $path)
    {
        $baseDir = 'uploads/' . $path;

        $name = sha1(time() . $file->getClientOriginalName());
        $extension = $file->getClientOriginalExtension();
        $fileName = "{$name}.{$extension}";

        $file->move(public_path() . '/' . $baseDir, $fileName);

        return "{$baseDir}/{$fileName}";
    }
}
/**
 * Upload
 */
if (!function_exists('upload')) {
    function upload($file, $path)
    {
        $baseDir = 'uploads/' . $path;

        $name = sha1(time() . $file->getClientOriginalName());
        $extension = $file->getClientOriginalExtension();
        $fileName = "{$name}.{$extension}";

        $file->move(public_path() . '/' . $baseDir, $fileName);

        return "{$baseDir}/{$fileName}";
    }
}
/**
 * Upload Storage
 */
if (!function_exists('uploadToStorage')) {
    function uploadToStorage($file, $path)
    {
        $baseDir = 'public/' . $path;

        $name = sha1(time() . $file->getClientOriginalName());
        $extension = $file->getClientOriginalExtension();
        $fileName = "{$name}.{$extension}";

        $fullPath = storage_path('app/' . $baseDir);
        
        try {
            if (!File::exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true); 
            }

            Storage::disk('public')->putFileAs($path, $file, $fileName);

            chmod($fullPath . '/' . $fileName, 0755);
            
        } catch (\Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage());
            return response()->json(['error' => 'File upload failed.'], 500);
        }

        return [
            'relativePath' => "{$path}/{$fileName}",
            'absolutePath' => asset('storage/' . $path . '/' . $fileName),
        ];
    }
}
/**
 * Upload Storage
 */
if (!function_exists('uploadStorage')) {
    function uploadStorage($file, $name, $path)
    {
        $baseDir = 'public/' . $path;
        $extension = $file->getClientOriginalExtension();
        $fileName = "{$name}.{$extension}";
        Storage::disk('local')->putFileAs($baseDir, $file, $fileName, 'public');
        return "{$path}/{$fileName}";
    }
}



/**
 * Generate random Color
 */
if (!function_exists('random_color_part')) {
    function random_color_part()
    {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generateRandomColor')) {
    function generateRandomColor()
    {
        return  random_color_part() . random_color_part() . random_color_part();
    }
}


/**
 * Round Amount
 */
if (!function_exists('roundAmount')) {
    function roundAmount($amount)
    {
        return round($amount, 2, PHP_ROUND_HALF_UP);
    }
}
/**
 * Round Amount Down
 */
if (!function_exists('roundAmountDown')) {
    function roundAmountDown($amount)
    {
        return number_format($amount, 2, '.', '');
    }
}

/**
 * Get Distance By Lat,Lng
 */
if (!function_exists('getDistanceByLatLng')) {
    function getDistanceByLatLng($latFrom, $lngFrom, $latTo, $lngTo)
    {
        $latFrom = deg2rad($latFrom);
        $lngFrom = deg2rad($lngFrom);
        $latTo = deg2rad($latTo);
        $lngTo = deg2rad($lngTo);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)));
        return array(
            'success' => true,
            'distanceValue' => $angle * 6371000,
            'distance' => $angle * 6371000,
        );
    }
}
