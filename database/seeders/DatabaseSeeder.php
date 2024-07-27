<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Http\Resources\CreatorSampleVideoResource;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\CampaignRequest;
use App\Models\Cat;
use App\Models\City;
use App\Models\Creator;
use App\Models\CreatorSamplevideo;
use App\Models\RequestVideo;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Admin::factory(2)->create();  
        Creator::factory()
        ->has(CreatorSamplevideo::factory()->count(random_int(1,3)), 'sampleVideos')
        ->has(Cat::factory()->count(random_int(1,3)), 'cats')
        ->count(10)
        ->create();

        Brand::factory()->has(Campaign::factory()
            ->has(CampaignRequest::factory()
                ->has(RequestVideo::factory()->count(random_int(1,3)), 'videos')
                ->count(random_int(1,10))
            , 'requests')
            ->has(Cat::factory()->count(random_int(1,3)), 'cats')
            ->has(City::factory()->count(random_int(1,3)), 'cities')
            ->count(random_int(1,15))
        , 'campaigns')
        ->has(Cat::factory()->count(random_int(1,3)), 'cats')
        ->count(15)
        ->create();
    }

}
