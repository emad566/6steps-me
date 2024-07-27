<?php
namespace Database\Factories;

use App\Models\Admin;
use App\Services\ImageCreationService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AdminFactory extends Factory
{
    protected $model = Admin::class;

    public function definition()
    {
        $image =  new ImageCreationService();
        return [
            'admin_name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'mobile' => generateSaudiMobileNumber($this->faker),
            'logo' => $image->createImage($this->faker->name, 'Admin'),
            'address' => $this->faker->address,
            'websit_url' => $this->faker->url,
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // You can modify this as needed
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
