<?php

namespace App\Http\Resources;

use App\Models\AppConstants;
use App\Services\CreatedUpdatedHuman;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
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
            'campaign_id' => $this->campaign_id,
            'campaign_no' => $this->campaign_no,
            'brand_name' => $this->brand?->brand_name,
            // 'campaign_status_trans' => trans($this->campaign_status?? AppConstants::$campain_states[0]),
            'campaign_status' => $this->campaign_status?? AppConstants::$campain_states[0],
            'reject_reason' => $this->reject_reason,
            'campaign_title' => $this->campaign_title,
            'campaign_description' => $this->campaign_description,
            'start_at' => $this->start_at,
            'close_at' => $this->close_at,
            'conditions' => $this->conditions,
            'product_image' => $this->product_image,
            'ugc_no' => $this->ugc_no,
            'ugc_videos_no' => $this->ugc_videos_no,
            'video_seconds_min' => $this->video_seconds_min,
            'video_seconds_max' => $this->video_seconds_max,
            'video_price' => $this->video_price,
            'total_price' => $this->total_price,
            'is_usg_show' => $this->is_usg_show == 1? true : false,
            'is_brand_show' => $this->is_brand_show == 1? true : false,
            'is_tiktok' => $this->is_tiktok == 1? true : false,
            'is_instagram' => $this->is_instagram == 1? true : false,
            'is_youtube' => $this->is_youtube == 1? true : false,
            'is_sent_to_content_creator' => $this->is_sent_to_content_creator == 1? true : false,
            'city_names' => $this->cities()->pluck('city_name')->toArray(),
            'cat_names' => $this->cats()->pluck('cat_name')->toArray(),

            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            ...$human->human,
        ];
    }
}
