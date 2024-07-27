<?php

namespace Database\Factories;

use App\Models\AppConstants;
use App\Models\RequestVideo;
use App\Models\CampaignRequest;
use App\Models\Campaign;
use App\Models\Brand;
use App\Models\Creator;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestVideoFactory extends Factory
{
    protected $model = RequestVideo::class;

    public function definition()
    {
        return [
            'video_no' => $this->faker->unique()->numberBetween(1000, 9999),
            'request_id' => CampaignRequest::factory(), // Create a related CampaignRequest
            'campaign_id' => Campaign::factory(), // Create a related Campaign
            'brand_id' => Brand::factory(), // Create a related Brand
            'creator_id' => Creator::factory(), // Create a related Creator
            'video_url' => $this->faker->url(),
            'video_image_path' => $this->faker->imageUrl(),
            'video_description' => $this->faker->paragraph(),
            'video_status' => $this->faker->randomElement(AppConstants::$video_states),
            'video_reject_reason' => $this->faker->optional()->sentence(),
            'deleted_at'        => null,
            'created_at'        => now(),
            'updated_at'        => now(),
        ];
    }
}
