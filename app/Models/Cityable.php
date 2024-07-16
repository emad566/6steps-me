<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Cityable extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'cityables';
    protected $primaryKey = 'id';

    protected $fillable = [
        'ciy_ciy_id',
        'ciyable_id',
        'ciyable_type',
        'created_at',
        'updated_at',
    ];

    public function creators(): MorphToMany
    {
        return $this->morphedByMany(Creator::class, 'cityable');
    }

    public function brands(): MorphToMany
    {
        return $this->morphedByMany(Brand::class, 'cityable');
    }

    public function campaigns(): MorphToMany
    {
        return $this->morphedByMany(Campaign::class, 'cityable');
    }
}
