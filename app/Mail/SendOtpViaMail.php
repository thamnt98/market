<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOtpViaMail extends Mailable
{
    use Queueable, SerializesModels;

    public  $otp;

    /**
     * Create a new message instance.
     * @param $otp
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.otp')
            ->subject('Xác thực người dùng IB MarketFinexia')
            ->with([
                'otp' => $this->otp
            ]);
    }
}
