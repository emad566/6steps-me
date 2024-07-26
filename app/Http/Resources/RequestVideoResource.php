<?php

namespace App\Http\Resources;

use App\Models\AppConstants;
use App\Services\CreatedUpdatedHuman;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestVideoResource extends JsonResource
{ 
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {   
        $human = new CreatedUpdatedHuman($this);
        return [
            'video_id' => $this->video_id,
            'video_no' => $this->video_no,
            'request_id' => $this->request_id,
            'campaign_id' => $this->campaign_id,
            'brand_id' => $this->brand_id,
            'creator_id' => $this->creator_id,
            'video_url' => $this->video_url,
            'video_image_path' => $this->video_image_path,
            'video_description' => $this->video_description,
            'video_status' => $this->video_status,
            'video_reject_reason' => $this->video_reject_reason,

            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            ...$human->human,
        ];
    }
}
