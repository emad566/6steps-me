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
        Schema::create('creators', function (Blueprint $table) {
            $table->bigIncrements('creator_id');
            $table->string('mobile')->unique();
            $table->string('otp')->nullable();
            $table->timestamp('otp_created_at')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('creator_name')->nullable();
            $table->string('logo')->nullable();
            $table->text('bio', 500)->nullable();
            $table->string('address')->nullable();
            $table->string('IBAN_no')->nullable();
            $table->string('Mawthooq_no')->nullable();
            $table->date('birth_date')->nullable();


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
        Schema::dropIfExists('creators');
    }
};
