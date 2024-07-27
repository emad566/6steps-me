<?php

namespace App\Http\Resources;

use App\Services\CreatedUpdatedHuman;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreatorResource extends JsonResource
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
            'creator_id' => $this->creator_id,
            'mobile' => $this->mobile,
            'creator_name' => $this->creator_name,
            'logo' => $this->logo,
            'bio' => $this->bio,
            'address' => $this->address,
            'IBAN_no' => $this->IBAN_no,
            'Mawthooq_no' => $this->Mawthooq_no,
            'birth_date' => $this->birth_date,
            'sampleVideos' =>   CreatorSampleVideoResource::collection($this->sampleVideos->sortByDesc('video_order_no'))->response()->getData(true)['data'] ?? [],
            'cats' =>   $this->cats->pluck('cat_name')->toArray(),

            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            ...$human->human
        ];
    }
}
