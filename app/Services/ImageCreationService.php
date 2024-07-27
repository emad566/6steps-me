<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImageCreationService
{
    public $localPath = '';

    public function __construct()
    {
        
    }

    public function createImage($imageCategory, $localPath)
    {
        $fullPath = storage_path('app/public/' . $localPath); 
        if (!File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true); 
        }

        // Create a blank image
        $width = 640;
        $height = 480;
        $image = imagecreatetruecolor($width, $height);

        // Allocate colors
        $bgColor = imagecolorallocate($image, 255, 255, 255); // White background
        $textColor = imagecolorallocate($image, 0, 0, 0); // Black text

        // Fill the image with background color
        imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);

        // Set the path for the image
        $fileName = strtolower($imageCategory) . '-' . generateRandomStringId(1) . '.png';
        $fullLocalPath = $localPath . '/' . $fileName;

        $fontPath  = public_path('\arial.ttf'); 
        $fontSize = 60;
        $angle = 0;
        $text = $imageCategory;

        // Center the text
        $bbox = imagettfbbox($fontSize, $angle, $fontPath, $text);
        $textWidth = $bbox[2] - $bbox[0];
        $textHeight = $bbox[1] - $bbox[7];
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2 + $textHeight;

        imagettftext($image, $fontSize, $angle, $x, $y, $textColor, $fontPath, $text);

        // Save the image
        if (imagepng($image, storage_path("app/public/{$fullLocalPath}"))) {
            $this->localPath = $fullLocalPath;
        }

        // Clean up
        imagedestroy($image);
        return $this->localPath;
    }
}
 
