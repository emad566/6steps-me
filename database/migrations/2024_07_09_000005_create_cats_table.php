<?php

use App\Models\Cat;
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
        Schema::create('cats', function (Blueprint $table) {
            $table->bigIncrements('cat_id');
            $table->string('cat_name')->unique();

            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        $cats = [
            [
                "cat_name" => "Music",
            ],
            [
                "cat_name" => "Restorante",
            ],
            [
                "cat_name" => "Player",
            ],
            [
                "cat_name" => "Store",
            ],
            [
                "cat_name" => "Famouse",
            ],
            [
                "cat_name" => "Cat1",
            ],
            [
                "cat_name" => "Cat2",
            ],
            [
                "cat_name" => "Cat3",
            ]
        ];

        foreach ($cats as $cat) {
            Cat::create($cat);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cats');
    }
};
