<?php

use App\Models\AppConstants;
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
        Schema::create('request_videos', function (Blueprint $table) {
            $table->bigIncrements('video_id');
            $table->string('video_no')->unique();
            $table->bigInteger('request_id')->unsigned();
            $table->bigInteger('campaign_id')->unsigned();
            $table->bigInteger('brand_id')->unsigned();
            $table->bigInteger('creator_id')->unsigned(); 
            $table->string('video_url'); 
            $table->string('video_image_path');
            $table->text('video_description', 500);
            
            $table->enum('video_status', AppConstants::$video_states)->default(AppConstants::$video_states['0']);
            $table->text('video_reject_reason', 1000)->nullable();

 
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_videos');
    }
};
