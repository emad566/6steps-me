<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedFormat;
use Illuminate\Database\Eloquent\Model;  
use Illuminate\Database\Eloquent\SoftDeletes; 

class Statusable extends Model
{
    use SoftDeletes, CreatedUpdatedFormat;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'statusables';
    protected $primaryKey = 'id'; 

    protected $fillable = [
        'id',
        'statusable_id',
        'statusable_type',
        'status',
        'reason',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

}
