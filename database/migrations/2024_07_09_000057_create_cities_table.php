<?php

use App\Models\City;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->bigIncrements('city_id');
            $table->string('city_name')->unique();
            $table->string('country_name');

            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        $cities = [
            [
                "country_name" => "Saudi Arabia",
                "city_name" => "Riyadh",
            ],
            [
                "country_name" => "Saudi Arabia",
                "city_name" => "Jeddah",
            ],
            [
                "country_name" => "Saudi Arabia",
                "city_name" => "Mecca",
            ],
            [
                "country_name" => "Saudi Arabia",
                "city_name" => "Medina",
            ],
            [
                "country_name" => "Saudi Arabia",
                "city_name" => "Dammam",
            ]
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
