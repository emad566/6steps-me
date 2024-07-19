<?php

namespace App\Http\Resources;

use App\Services\CreatedUpdatedHuman;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
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
            'admin_name' => $this->admin_name,
            'email_verified_at' => $this->email_verified_at, 
            'mobile' => $this->mobile,

            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            ...$human->human

        ];  
    }
}
