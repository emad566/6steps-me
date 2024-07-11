<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Traits\CreatedUpdatedFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Carbon;

class Creator extends Authenticatable
{
    use HasApiTokens, SoftDeletes, Notifiable, CreatedUpdatedFormat;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'creators';
    protected $primaryKey = 'creator_id';

    protected $fillable = [
        'creator_id',
        'mobile',
        'otp',
        'otp_created_at',
        'email',
        'email_verified_at',
        'password',
        'creator_name',
        'logo',
        'bio',
        'address',
        'IBAN_no',
        'Mawthooq_no',
        'brith_date',

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


}
