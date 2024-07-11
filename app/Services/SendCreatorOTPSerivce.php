<?php
namespace App\Services;

use Carbon\Carbon;

class SendCreatorOTPSerivce
{
    public $otp = '';
    function __construct($creator)
    {

        $creator->update(['otp' => rand(1000, 9999), 'otp_created_at' => Carbon::now()]);
        $this->otp = $creator->otp;
    }
}

