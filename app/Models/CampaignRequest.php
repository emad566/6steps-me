<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class CampaignRequest extends Model
{
    use SoftDeletes, CreatedUpdatedFormat;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'campaign_request';
    protected $primaryKey = 'request_id'; 

    protected $fillable = [
        'request_id',
        'request_no',
        'campaign_id',
        'brand_id',
        'creator_id',
        'explanation',
        'request_status',
        'reject_reason',

        'deleted_at',
        'created_at',
        'updated_at',
    ];

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
        return $this->hasMany(Statusable::class, 'statusable_id', 'request_id')->where('statusable_type', 'CampaignRequest');
    }
 
}
