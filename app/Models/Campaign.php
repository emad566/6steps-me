<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

        'deleted_at',
        'created_at',
        'updated_at',
    ];
}
