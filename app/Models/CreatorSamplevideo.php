<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Traits\CreatedUpdatedFormat;
use Illuminate\Database\Eloquent\Model; 

class CreatorSamplevideo extends Model
{
    use CreatedUpdatedFormat;

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
        'video_image_path',
        'video_description',


    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function getVideoImagePathAttribute($value)
    {
        return $value? asset('storage/' . $value) : '';
    }
}
