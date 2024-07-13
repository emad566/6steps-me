<?php

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
        Schema::create('creator_samplevideos', function (Blueprint $table) {
            $table->bigIncrements('samplevideo_id');
            $table->bigInteger('creator_id')->unsigned();
            $table->string('video_url');
            $table->integer('video_order_no')->default(1);
            $table->string('video_image_path')->nullable();
            $table->text('video_description', 500)->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creator_samplevideos');
    }
};
