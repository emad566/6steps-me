<?php
namespace App\Services;

use App\Mail\SendCodeMail; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class SendEmailOTPSerivce
{
    public $otp = '';
    public $item = '';
    function __construct($item)
    {
        $this->item = $item;
        $item->update(['otp' => rand(1000, 9999), 'otp_created_at' => Carbon::now()]);
        $this->otp = $item->otp;

        $this->send();
    }

    protected function send() {
        Mail::send(new SendCodeMail($this->item->email, $this->otp));
    }
}

