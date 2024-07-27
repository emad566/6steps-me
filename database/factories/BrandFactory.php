<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Services\ImageCreationService;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition()
    {
        $image =  new ImageCreationService();

        return [
            'brand_name'        => $this->faker->unique()->company,
            'mobile'            => generateSaudiMobileNumber($this->faker),
            'email'             => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'otp'               => '',
            'otp_created_at'    => now(),
            'password'          => bcrypt($this->faker->password), // Using bcrypt for password
            'logo'              => $image->createImage(str_replace([',', ' ', '.'], '-', $this->faker->name), 'Brand'),
            'website_url'       => $this->faker->url,
            'description'       => $this->faker->text,
            'address'           => $this->faker->address,
            'branches_no'       => $this->faker->numberBetween(1, 10),
            'tax_no'            => $this->faker->numerify('#########'),
            'cr_no'             => $this->faker->numerify('CR########'),
            'deleted_at'        => null,
            'created_at'        => now(),
            'updated_at'        => now(),
        ];
    }

    
}
