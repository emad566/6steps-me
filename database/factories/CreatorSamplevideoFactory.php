<?php

namespace Database\Factories;

use App\Models\CreatorSamplevideo;
use App\Services\ImageCreationService;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreatorSamplevideoFactory extends Factory
{
    protected $model = CreatorSamplevideo::class;

    public function definition()
    {
        $image =  new ImageCreationService();

        return [
            'creator_id'        => \App\Models\Creator::factory(), // Link to a Creator factory instance
            'video_url'         => $this->faker->url,
            'video_order_no'    => $this->faker->numberBetween(1, 100),
            'video_image_path'  => $image->createImage($this->faker->company, 'Creator/SampleVideos'),
            'video_description'  => $this->faker->sentence,
        ];
    }
}
