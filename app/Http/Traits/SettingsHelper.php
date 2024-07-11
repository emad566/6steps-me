<?php

namespace App\Http\Controllers\Api\Helpers;
use App\Models\Setting;

trait SettingsHelper
{
    public function value($key): int
    {
        return Setting::where('set_key', $key)->first()->set_value;
    }
}
