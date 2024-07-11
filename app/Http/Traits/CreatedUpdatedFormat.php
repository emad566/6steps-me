<?php

namespace App\Http\Traits;

use Illuminate\Support\Carbon;

trait CreatedUpdatedFormat
{
    public function getCreatedAtAttribute($value)
    {
        if (!$value) return $value;
        $createdAt = Carbon::parse($value);
        return $createdAt->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($value)
    {
        if (!$value) return $value;
        $updatedAt = Carbon::parse($value);
        return $updatedAt->format('Y-m-d H:i:s');
    }

    public function getDeletedAtAttribute($value)
    {
        if(!$value) return $value;
        $deletedAt = Carbon::parse($value);
        return $deletedAt->format('Y-m-d H:i:s');
    }
}
