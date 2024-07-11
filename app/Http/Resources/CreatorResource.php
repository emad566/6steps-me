<?php

namespace App\Http\Resources;

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
        return [
            'creator_id' => $this->creator_id,
            'mobile' => $this->mobile,
            'creator_name' => $this->creator_name,
            'logo' => $this->logo,
            'bio' => $this->bio,
            'address' => $this->address,
            'IBAN_no' => $this->IBAN_no,
            'Mawthooq_no' => $this->Mawthooq_no,
            'brith_date' => $this->brith_date,

            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
