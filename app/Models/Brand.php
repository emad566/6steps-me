<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Traits\CreatedUpdatedFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Brand extends Authenticatable
{
    use HasApiTokens, SoftDeletes, CreatedUpdatedFormat, Notifiable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'brands';
    protected $primaryKey = 'brand_id';

    protected $fillable = [
        'brand_id',
        'brand_name',
        'mobile',
        'email',
        'email_verified_at',
        'otp',
        'otp_created_at',
        'password',
        'logo',
        'website_url',
        'description',
        'address',
        'branches_no',
        'tax_no',
        'cr_no',

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

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    public function getLogoAttribute($value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        } 
        return $value ? asset('storage/' . $value) : '';
    }

    public function isCompleteProfile() {
        return $this->address? true : false;
    }

    public function cats(): MorphToMany
    {
        return $this->morphToMany(Cat::class, 'catable');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'brand_id', 'brand_id');
    }
}
