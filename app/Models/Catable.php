<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Catable extends Authenticatable
{
    use HasApiTokens, Notifiable;

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
}
