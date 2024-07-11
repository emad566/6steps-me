<?php

namespace App\Http\Traits;


use App\Mail\SendCodeMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

trait AuthApiHelper
{
    public function update_code($user): int
    {
        $verification_code = rand(100000,999999);
        $user->update(['verification_code'=>$verification_code]) ;
        return $verification_code;
    }

    public function send_code($to, $code): void
    {
        Mail::send(new SendCodeMail($to, $code));
    }

    public function verify_email_by_code($email, $code)
    {
        $user = User::where('email', $email)->where('verification_code', $code)->first();
        if($user){
            $user->update([
               'verification_code'=>NULL,
               'email_verified_at' => Carbon::now()
            ]);
        }
        return (bool)$user;
    }

    public function change_password_by_code($email, $code, $password)
    {
        $user = User::where('email', $email)->where('verification_code', $code)->first();
        if($user){
            $user->update([
                'verification_code'=>NULL,
                'password' => $password
            ]);
        }
        return (bool)$user;
    }
}
