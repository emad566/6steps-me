<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Catable extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'catables';
    protected $primaryKey = 'id';

    protected $fillable = [
        'cat_cat_id',
        'catable_id',
        'catable_type',
        'created_at',
        'updated_at',
    ];

    public function creators(): MorphToMany
    {
        return $this->morphedByMany(Creator::class, 'catable');
    }

    public function brands(): MorphToMany
    {
        return $this->morphedByMany(Brand::class, 'catable');
    }

    public function campaigns(): MorphToMany
    {
        return $this->morphedByMany(Campaign::class, 'catable');
    }
}
