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
        Schema::create('statusables', function (Blueprint $table) {
            $table->bigIncrements('id'); 
            $table->bigInteger('statusable_id')->unsigned();  
            $table->enum('statusable_type', AppConstants::$statusable_types);
            $table->enum('status', [...AppConstants::$campain_states, ...AppConstants::$request_states, ...AppConstants::$video_states]); 
            $table->text('reason', '1000')->nullable();
            
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
        Schema::dropIfExists('statusables');
    }
};
