<?php

namespace App\Http\Traits;

use Illuminate\Support\Carbon;

trait CreatedUpdatedFormat
{
    public function getCreatedAtAttribute($value)
    {
        if (!$value) return $value;  
        return Carbon::parse($value)->setTimezone('UTC'); 
    }

    public function getUpdatedAtAttribute($value)
    {
        if (!$value) return $value;  
        return Carbon::parse($value)->setTimezone('UTC'); 
    }

    public function getDeletedAtAttribute($value)
    {
        if (!$value) return $value;  
        return Carbon::parse($value)->setTimezone('UTC'); 
    }
}
