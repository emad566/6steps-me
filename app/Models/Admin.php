<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Traits\CreatedUpdatedFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, SoftDeletes, Notifiable, CreatedUpdatedFormat, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'admins';
    protected $primaryKey = 'admin_id';

    protected $fillable = [
        'admin_id',
        'admin_name',
        'email',
        'mobile',
        'logo', 
        'address',
        'websit_url',
        'email_verified_at',
        'password',

        'deleted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getLogoAttribute($value)
    {
        
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        } 
        return $value ? asset('storage/' . $value) : '';
    }
}
