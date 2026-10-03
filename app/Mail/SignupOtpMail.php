<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SignupOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $otp;
    public int $expiryMinutes;

    public function __construct(string $name, string $otp, int $expiryMinutes = 10)
    {
        $this->name = $name;
        $this->otp = $otp;
        $this->expiryMinutes = $expiryMinutes;
    }

    public function build()
    {
        return $this->subject(__('Verify Your Mail ID — Wappiyo Security Code'))
                    ->view('emails.signup_otp')
                    ->with([
                        'name' => $this->name,
                        'otp' => $this->otp,
                        'expiryMinutes' => $this->expiryMinutes,
                    ]);
    }
}
