<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Campaign extends Model
{
    use SoftDeletes, CreatedUpdatedFormat;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'campaigns';
    protected $primaryKey = 'campaign_id'; 

    protected $fillable = [
        'campaign_id',
        'campaign_no',
        'brand_id',
        'campaign_title',
        'campaign_description',
        'start_at',
        'close_at',
        'conditions',
        'product_image',
        'ugc_no',
        'ugc_videos_no',
        'video_seconds_min',
        'video_seconds_max',
        'video_price',
        'total_price',
        'is_usg_show',
        'is_brand_show',
        'is_tiktok',
        'is_instagram',
        'is_youtube',
        'is_sent_to_content_creator',
        'campaign_status',
        'reject_reason',

        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function getProductImageAttribute($value)
    {
        return $value? asset('storage/' . $value) : '';
    }

    public function brand(): HasOne
    {
        return $this->hasOne(Brand::class, 'brand_id', 'brand_id');
    }

    public function cats(): MorphToMany
    {
        return $this->morphToMany(Cat::class, 'catable');
    }

    public function cities(): MorphToMany
    {
        return $this->morphToMany(City::class, 'cityable');
    }

    public function statusables()
    {
        return $this->hasMany(Statusable::class, 'statusable_id', 'campaign_id')->where('statusable_type', 'Campaign');
    }
}
