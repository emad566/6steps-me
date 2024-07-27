<?php

namespace App\Http\Resources;

use App\Models\AppConstants;
use App\Services\CreatedUpdatedHuman;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignRequestResource extends JsonResource
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
            'request_id' => $this->request_id,
            'request_no' => $this->request_no,
            'campaign_id' => $this->campaign_id,
            'campaign_title' => $this->campaign?->campaign_title,
            'campaign_status' => $this->campaign?->campaign_status,
            'reject_reson' => $this->campaign?->reject_reson,
            'brand_id' => $this->brand_id,
            'brand_name' => $this->brand?->brand_name,
            'creator_id' => $this->creator_id,
            'creator_name' => $this->creator?->creator_name,
            'explanation' => $this->explanation,
            'request_status' => $this->request_status,
            'request_reject_reason' => $this->request_reject_reason, 

            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            ...$human->human,
        ];
    }
}
