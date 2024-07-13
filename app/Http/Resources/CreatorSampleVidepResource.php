<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreatorSampleVidepResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'video_url' => $this->video_url,
            'video_order_no' => $this->video_order_no,
            'video_image_path' => $this->video_image_path,
            'video_description' => $this->video_description,
        ];
    }
}
