<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Traits\CreatedUpdatedFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cat extends Model
{
    use SoftDeletes, CreatedUpdatedFormat;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'cats';
    protected $primaryKey = 'cat_id';

    protected $fillable = [
        'cat_name',

        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function creatrs(): MorphToMany
    {
        return $this->morphedByMany(Creator::class, 'catable');
    }

    public function campaigns(): MorphToMany
    {
        return $this->morphedByMany(Campaign::class, 'catable');
    }
}
