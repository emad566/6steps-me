<?php

namespace Database\Factories;

use App\Models\AppConstants;
use App\Models\CampaignRequest;
use App\Models\Campaign;
use App\Models\Brand;
use App\Models\Creator;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignRequestFactory extends Factory
{
    protected $model = CampaignRequest::class;

    public function definition()
    {
        return [
            'request_no' => $this->faker->unique()->numberBetween(1000, 9999),
            'campaign_id' => Campaign::factory(), // Create a related Campaign
            'brand_id' => Brand::factory(), // Create a related Brand
            'creator_id' => Creator::factory(), // Create a related Creator
            'explanation' => $this->faker->paragraph(),
            'request_status' => $this->faker->randomElement(AppConstants::$request_states),
            'request_reject_reason' => $this->faker->optional()->sentence(),
            'deleted_at'        => null,
            'created_at'        => now(),
            'updated_at'        => now(),
        ];
    }
}
