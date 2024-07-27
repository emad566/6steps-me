<?php

namespace Database\Factories;

use App\Models\Creator;
use App\Services\ImageCreationService;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreatorFactory extends Factory
{
    protected $model = Creator::class;

    public function definition()
    {
        $image =  new ImageCreationService();
        return [
            'mobile'            => generateSaudiMobileNumber($this->faker),
            'otp'               => '',
            'otp_created_at'    => now(),
            'email'             => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password'          => bcrypt('password'), // Use a default password
            'creator_name'      => $this->faker->name,
            'logo'              => $image->createImage($this->faker->name, 'Creator'),
            'bio'               => $this->faker->paragraph,
            'address'           => $this->faker->address,
            'IBAN_no'           => $this->faker->bankAccountNumber,
            'Mawthooq_no'       => $this->faker->numerify('MT######'),
            'birth_date'        => $this->faker->date(),
            'deleted_at'        => null,
            'created_at'        => now(),
            'updated_at'        => now(),
        ];
    }
}
