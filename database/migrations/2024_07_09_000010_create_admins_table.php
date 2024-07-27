<?php

use App\Models\Admin;
use App\Services\ImageCreationService;
use Carbon\Carbon;
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
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('admin_id');
            $table->string('admin_name');
            $table->string('email')->unique();
            $table->string('mobile')->unique();
            $table->string('logo')->default('user.png');
            $table->string('address')->nullable();
            $table->string('websit_url')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
        
        $image =  new ImageCreationService();

        Admin::create([
            'email' => 'admin@6stepsa.com',
            'password' => '123456',
            'mobile' => '966233555011',
            'admin_name' => '6stepsa',
            'email_verified_at' => Carbon::now(),
        ]);

        for ($i=1; $i <5 ; $i++) { 
            Admin::create([
                'email' => "admin$i@6stepsa.com",
                'password' => '123456',
                'mobile' => '96623355508'.$i,
                'admin_name' => '6stepsa' . $i,
                'email_verified_at' => Carbon::now(),
                'logo' => $image->createImage('6stepsa' . $i, 'Admin')
            ]);
        }
         
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
