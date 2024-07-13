<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'brand_id' => $this->brand_id,
            'brand_name' => $this->brand_name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'logo' => $this->logo,
            'website_url' => $this->website_url,
            'description' => $this->description,
            'address' => $this->address,
            'branches_no' => $this->branches_no,
            'tax_no' => $this->tax_no,
            'cr_no' => $this->cr_no,

            'cats' =>   $this->cats->pluck('cat_name')->toArray(),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
