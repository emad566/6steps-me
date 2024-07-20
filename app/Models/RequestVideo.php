<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedFormat;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes; 

class RequestVideo extends Model
{
    use SoftDeletes, CreatedUpdatedFormat;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'request_videos';
    protected $primaryKey = 'video_id'; 

    protected $fillable = [
        'video_id',
        'video_no',
        'request_id',
        'campaign_id',
        'brand_id',
        'creator_id',
        'video_url',
        'video_image_path',
        'video_description',
        'video_status',
        'video_reject_reason',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function request(): HasOne
    {
        return $this->hasOne(CampaignRequest::class, 'request_id', 'request_id');
    }

    public function campaign(): HasOne
    {
        return $this->hasOne(Campaign::class, 'campaign_id', 'campaign_id');
    }

    
    public function brand(): HasOne
    {
        return $this->hasOne(Brand::class, 'brand_id', 'brand_id');
    }
    
    public function creator(): HasOne
    {
        return $this->hasOne(Creator::class, 'creator_id', 'creator_id');
    }
 
    public function statusables()
    {
        return $this->hasMany(Statusable::class, 'statusable_id', 'video_id')->where('statusable_type', 'RequestVideo');
    }
 
}
