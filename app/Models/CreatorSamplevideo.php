<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CreatorSamplevideo extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'creator_samplevideos';
    protected $primaryKey = 'samplevideo_id';

    protected $fillable = [
        'creator_id',
        'video_url',
        'video_order_no',
        'video_image_url',
        'video_description',
        
        'created_at',
        'updated_at',
    ];
}
