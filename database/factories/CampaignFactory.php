<?php

namespace Database\Factories;

use App\Models\AppConstants;
use App\Models\Brand;
use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition()
    {
        return [
            'campaign_no' => $this->faker->unique()->numberBetween(1000, 9999),
            'brand_id' => Brand::factory(), 
            'campaign_title' => $this->faker->sentence(),
            'campaign_description' => $this->faker->paragraph(),
            'start_at' => $this->faker->dateTimeBetween('now', '+1 month'),
            'close_at' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'conditions' => $this->faker->text(),
            'product_image' => $this->faker->imageUrl(),
            'ugc_no' => $this->faker->numberBetween(1, 100),
            'ugc_videos_no' => $this->faker->numberBetween(1, 100),
            'video_seconds_min' => $this->faker->numberBetween(10, 60),
            'video_seconds_max' => $this->faker->numberBetween(60, 300),
            'video_price' => random_int(2, 500), 
            'is_usg_show' => $this->faker->boolean(),
            'is_brand_show' => $this->faker->boolean(),
            'is_tiktok' => $this->faker->boolean(),
            'is_instagram' => $this->faker->boolean(),
            'is_youtube' => $this->faker->boolean(),
            'is_sent_to_content_creator' => $this->faker->boolean(),
            'campaign_status' => $this->faker->randomElement(AppConstants::$campain_states),
            'reject_reason' => $this->faker->optional()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null, // Or $this->faker->dateTimeBetween('-1 year', 'now') for soft deletes
        ];
    }
}
