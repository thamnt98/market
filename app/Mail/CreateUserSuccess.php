<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreateUserSuccess extends Mailable
{
    use Queueable, SerializesModels;

      /**
     * @var array
     */
    protected $user;

    /**
     * @var $token
     */
    protected $token;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.createuser')
        ->subject('Chào mừng bạn đến với Gemi Broker!')
        ->with([
            'firstName' => $this->user['first_name'],
            'lastName' => $this->user['last_name'],
            'url' => 'https://accounts.gemifx.com/password/reset'. '?token=' . $this->token .
                '&email=' . urlencode($this->user['email'])
        ]);
    }
}
